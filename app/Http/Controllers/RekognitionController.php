<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

use App\Models\SinhVien;
use App\Models\LichThi;
use App\Models\DiemDanh;

class RekognitionController extends Controller
{
    private $bucket = 'diemdanh-sinhvien';
    private $collection = 'sinhvien_faces';

    // =============================
    // VIEW
    // =============================
    public function uploadForm()
    {
        return view('rekognition.train', ['hideSearch' => true]);
    }

    public function index($lichThi)
    {
        $lichThi = LichThi::findOrFail($lichThi);

        $query = DiemDanh::with('sinhVien')
            ->where('lich_thi_id', $lichThi->id);

        if (request('search')) {
            $search = request('search');
            $query->whereHas('sinhVien', function ($q) use ($search) {
                $q->where('ma_sv', 'like', "%{$search}%")
                  ->orWhere('ho_ten', 'like', "%{$search}%");
            });
        }

        if (request('chua_diem_danh')) {
            $query->where(function ($q) {
                $q->where('ket_qua', '!=', 'hợp lệ')
                  ->orWhereNull('ket_qua');
            });
        }

        $sinhViens = $query->get();

        return view('rekognition.index', compact('lichThi', 'sinhViens'));
    }

    // =============================
    // TRAIN (CALL LAMBDA)
    // =============================
    public function trainAjax(Request $request)
{
    return $this->handleTrain($request, false);
}

public function retrainAjax(Request $request)
{
    return $this->handleTrain($request, true);
}

private function handleTrain(Request $request, $force = false)
{
    try {
        $request->validate([
            'ma_sv' => 'required',
            'hinh_anh' => 'required|mimes:jpg,jpeg,png|max:5120',
        ]);

        $ma_sv = strtoupper(trim($request->ma_sv));
        $image = $request->file('hinh_anh');

        $sv = SinhVien::where('ma_sv', $ma_sv)->first();

        if (!$sv) {
            return $this->error("Không tồn tại MSSV: $ma_sv");
        }

        if ($sv->da_train_khuon_mat && !$force) {
            return $this->error('Sinh viên đã có mẫu khuôn mặt');
        }

        if ($force && !$sv->canRetrain()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đủ điều kiện train lại',
                'can_retrain' => false
            ]);
        }

        // =========================
        // 🔹 Upload temp
        // =========================
        $tempPath = "temp/{$ma_sv}_" . uniqid() . ".jpg";

        Storage::disk('s3')->put(
            $tempPath,
            file_get_contents($image),
            'public'
        );

        // =========================
        // 🔹 Call Lambda
        // =========================
        $lambdaUrl = $force
            ? env('LAMBDA_RETRAIN_URL')
            : env('LAMBDA_TRAIN_URL');

        $response = Http::post($lambdaUrl, [
            'bucket' => $this->bucket,
            'imageKey' => $tempPath,
            'tempKey' => $tempPath, // dùng chung cho retrain
            'collectionId' => $this->collection,
            'externalImageId' => $ma_sv,
        ]);

        if (!$response->ok()) {
            return $this->error('Không gọi được Lambda');
        }

        $data = $this->parseLambdaResponse($response);

        if (!$data['success']) {
            return $this->error($data['message'] ?? 'Train thất bại');
        }

        // =========================
        // 🔹 SAVE DB
        // =========================

        // 1. Lấy image_key từ Lambda trả về
        $imageKey = $data['image_key'] ?? "sinhvien/{$ma_sv}.jpg";
        
        // 2. Tạo Full URL cho ảnh
        $imageUrl = Storage::disk('s3')->url($imageKey);
        
        // Thêm timestamp để tránh cache trình duyệt
        // Vì Retrain sẽ ghi đè lên file cũ, nếu không có ?v=..., trình duyệt sẽ vẫn hiện ảnh cũ.
        $imageUrlWithCacheBuster = $imageUrl . '?v=' . time();

        $sv->update([
            'da_train_khuon_mat' => true,
            'face_ids' => $data['face_ids'] ?? [],
            'hinh_anh' => $imageUrlWithCacheBuster,
            'so_lan_nhan_dien' => 0,
            'do_chinh_xac_tb' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $force ? 'Train lại thành công' : 'Train thành công',
            'face_count' => count($data['face_ids'] ?? []),
            'image_url'  => $imageUrlWithCacheBuster
        ]);

    } catch (\Throwable $e) {
        return $this->error($e->getMessage(), 500);
    }
}

    // =============================
    // COMPARE (CALL LAMBDA)
    // =============================
    public function compareMany(Request $request, LichThi $lichThi)
    {
        $request->validate([
            'hinh_anh_base64' => 'required',
        ]);

        try {
            // decode base64
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->hinh_anh_base64);
            $imageBytes = base64_decode($base64);

            // upload S3
            $fileName = 'temp/' . uniqid() . '.jpg';
            Storage::disk('s3')->put($fileName, $imageBytes, 'public');

            // call lambda
            $response = Http::post(env('LAMBDA_COMPARE_URL'), [
                'bucket' => $this->bucket,
                'imageKey' => $fileName,
                'collectionId' => $this->collection,
                'threshold' => 85
            ]);

            $responseData = $response->json();

            if (isset($responseData['body'])) {
                $body = json_decode($responseData['body'], true);
            } else {
                $body = $responseData;
            }

            $faces = [];

            // matched
            foreach ($body['attendees'] as $a) {
                $sv = SinhVien::where('ma_sv', $a['externalImageId'])->first();

                $checked = false;
                if ($sv) {
                    $checked = DiemDanh::where('sinh_vien_id', $sv->id)
                        ->where('lich_thi_id', $lichThi->id)
                        ->where('ket_qua', 'hợp lệ')
                        ->exists();
                }

                $faces[] = [
                    'box' => [
                        'x' => $a['box']['left'],
                        'y' => $a['box']['top'],
                        'width' => $a['box']['width'],
                        'height' => $a['box']['height'],
                    ],
                    'name' => $a['externalImageId'],
                    'ho_ten' => $sv->ho_ten ?? null,
                    'similarity' => $a['similarity'],
                    'valid' => (bool)$sv,
                    'checkedIn' => $checked,
                    'color' => $checked ? 'yellow' : 'green'
                ];
            }

            // unknown
            foreach ($body['unknownFaces'] as $u) {
                $ly_do = $u['reason'] ?? $u['error'] ?? 'Không xác định';
                $debug_scores = $u['debug_scores'] ?? null;

                // Ghi log vào file storage/logs/laravel.log
                \Log::warning("Phát hiện khuôn mặt không đạt chuẩn điểm danh", [
                    'ly_do' => $ly_do,
                    'toa_do_box' => $u['box'],
                    'chi_so_chi_tiet' => [
                        'do_net_sharpness' => $debug_scores['sharpness'] ?? 'N/A',
                        'do_sang_brightness' => $debug_scores['brightness'] ?? 'N/A',
                        'kich_thuoc_face' => $debug_scores['facePixelWidth'] ?? 'N/A',
                        'quay_ngang' => $debug_scores['yaw'] ?? 'N/A',
                        'cui_ngua' => $debug_scores['pitch'] ?? 'N/A'
                    ]
                ]);
                $faces[] = [
                    'box' => [
                        'x' => $u['box']['left'],
                        'y' => $u['box']['top'],
                        'width' => $u['box']['width'],
                        'height' => $u['box']['height'],
                    ],
                    'name' => null,
                    'ho_ten' => null,
                    'similarity' => null,
                    'valid' => false,
                    'checkedIn' => false,
                    'color' => 'red',
                    'reason' => $ly_do
                ];
            }

            session(["faces_{$lichThi->id}" => $faces]);

            return response()->json([
                'faces' => $faces
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =============================
    // CONFIRM ĐIỂM DANH
    // =============================
    public function confirmMany(Request $request, LichThi $lichThi)
    {
        $request->validate([
            'faces' => 'required|json',
        ]);

        $faceIndexes = json_decode($request->faces, true);
        $faces = session("faces_{$lichThi->id}", []);

        foreach ($faceIndexes as $idx) {
            if (!isset($faces[$idx])) continue;

            $face = $faces[$idx];

            if (!$face['valid'] || !$face['name']) continue;

            $sv = SinhVien::where('ma_sv', $face['name'])->first();

            DiemDanh::where('sinh_vien_id', $sv->id)
                ->where('lich_thi_id', $lichThi->id)
                ->update([
                    'ket_qua' => 'hợp lệ',
                    'do_chinh_xac' => $face['similarity'],
                    'thoi_gian_dd' => now(),
                    'hinh_thuc_dd' => 'Camera'
                ]);

            $faces[$idx]['checkedIn'] = true;
            $faces[$idx]['color'] = 'yellow';
        }

        session(["faces_{$lichThi->id}" => $faces]);

        return response()->json([
            'message' => 'Điểm danh thành công',
            'faces' => $faces
        ]);
    }

    public function getAttendanceData(LichThi $lichThi)
    {
        $query = DiemDanh::with('sinhVien')
            ->where('lich_thi_id', $lichThi->id);

        if (request('search')) {
            $search = request('search');
            $query->whereHas('sinhVien', function($q) use ($search) {
                $q->where('ma_sv', 'like', "%{$search}%")
                ->orWhere('ho_ten', 'like', "%{$search}%");
            });
        }

        if (request('chua_diem_danh')) {
            $query->where('ket_qua', '!=', 'hợp lệ')
                ->orWhereNull('ket_qua');
        }

        $sinhViens = $query->get();

        return view('rekognition.attendance_table', compact('sinhViens', 'lichThi'));
    }

    private function parseLambdaResponse($response)
    {
        $json = $response->json();

        return isset($json['body'])
            ? json_decode($json['body'], true)
            : $json;
    }

    private function error($message, $code = 200)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
    public function deleteFace(Request $request, $studentId)
    {
        try {

            $ma_sv = strtoupper(trim($studentId));
            $sv = SinhVien::where('ma_sv', $ma_sv)->first();

            if (!$sv) {
            return redirect()->back()->with('error', "Không tồn tại MSSV: $ma_sv");
            }

            if (!$sv->da_train_khuon_mat) {
                return redirect()->back()->with('error', 'Sinh viên này chưa có dữ liệu khuôn mặt để xóa');
            }

            // =========================
            //  GỌI LAMBDA
            // =========================
            $lambdaUrl = env('LAMBDA_DELETE_URL');

            $response = Http::post($lambdaUrl, [
                'bucket'          => $this->bucket,
                'collectionId'    => $this->collection,
                'externalImageId' => $ma_sv,
            ]);

            if (!$response->ok()) {
                return redirect()->back()->with('error', 'Không gọi được Lambda xóa dữ liệu');
            }

            $data = $this->parseLambdaResponse($response);

            if (empty($data['success'])) {
                return redirect()->back()->with('error', $data['message'] ?? 'Xóa dữ liệu khuôn mặt thất bại');
            }

            // =========================
            // CẬP NHẬT DATABASE
            // =========================
            // Đặt lại các trạng thái về như lúc chưa train
            $sv->update([
                'da_train_khuon_mat' => false,
                'face_ids'           => [],
                'hinh_anh'      => null,
                'so_lan_nhan_dien'   => 0,
                'do_chinh_xac_tb'    => null,
            ]);

            $soLuongMat = $data['deleted_faces'] ?? 0;
            return redirect()->back()->with('success', "Xóa dữ liệu khuôn mặt thành công (đã xóa $soLuongMat khuôn mặt).");

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', "Đã xảy ra lỗi: " . $e->getMessage());
        }
    }
}
