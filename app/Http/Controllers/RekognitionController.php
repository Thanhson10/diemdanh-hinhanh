<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

use App\Models\SinhVien;
use App\Models\LichThi;
use App\Models\DiemDanh;
use App\Models\AnhTrainSv;

class RekognitionController extends Controller
{
    private $bucket = 'diemdanh-sinhvien';
    private $collection = 'sinhvien_faces';

    // VIEW
    public function uploadForm()
    {
        AnhTrainSv::cleanupFailedSafe();
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

    // TRAIN (CALL LAMBDA)
    public function trainAjax(Request $request)
{
    return $this->handleTrain($request);
}

private function handleTrain(Request $request)
{
    if (!defined('CURL_SSLVERSION_TLSv1_2')) {
        define('CURL_SSLVERSION_TLSv1_2', 6);
    }

    try {
        $request->validate([
            'ma_sv' => 'required',
            'hinh_anh' => 'required|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ma_sv = strtoupper(trim($request->ma_sv));
        $image = $request->file('hinh_anh');

        $sv = SinhVien::where('ma_sv', $ma_sv)->first();

        if (!$sv) {
            return $this->error("Không tồn tại MSSV: $ma_sv");
        }

        // Tạo hash
        $fileContent = file_get_contents($image);
        $fileHash = md5($fileContent);
        $fileName = $image->getClientOriginalName();

        // Check trùng
        $exists = AnhTrainSv::where('sinh_vien_id', $sv->id)
            ->where('file_hash', $fileHash)
            ->where('trang_thai', 'trained')
            ->exists();

        if ($exists) {
            return $this->error("Ảnh này đã được upload trước đó");
        }

        // Check limit
        $currentCount = $sv->danhSachAnhTrain()
            ->where('trang_thai', 'trained')
            ->count();

        $maxLimit = 2;

        if ($currentCount >= $maxLimit) {
            return $this->error("Sinh viên đã đạt giới hạn $maxLimit ảnh");
        }

        // Tạo record
        $anhTrain = AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'file_hash'    => $fileHash,
            'file_name'    => $fileName,
            'trang_thai'   => 'pending',
        ]);

        // Upload S3
        $tempPath = "temp/{$ma_sv}_" . uniqid() . ".jpg";
        Storage::disk('s3')->put($tempPath, $fileContent, 'public');

        // GỌI LAMBDA
        $lambdaUrl = env('LAMBDA_TRAIN_URL');

        $response = Http::post($lambdaUrl, [
            'bucket' => $this->bucket,
            'imageKey' => $tempPath,
            'collectionId' => $this->collection,
            'externalImageId' => $ma_sv,
        ]);

        if (!$response->ok()) {
            $anhTrain->update(['trang_thai' => 'failed']);
            return $this->error('Không gọi được Lambda');
        }

        $data = $this->parseLambdaResponse($response);

        if (!$data['success']) {
            $anhTrain->update(['trang_thai' => 'failed']);
            return $this->error($data['message'] ?? 'Train thất bại');
        }

        // SUCCESS
        $faceIds = $data['face_ids'] ?? [];
        $faceId = count($faceIds) > 0 ? $faceIds[0] : null;

        $finalPath = $data['image_key'];

        $anhTrain->update([
            'hinh_anh'   => $finalPath,
            'face_id'    => $faceIds,
            'trang_thai' => 'trained',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tải ảnh và train thành công',
            'image_url' => Storage::disk('s3')->url($finalPath)
        ]);

    } catch (\Throwable $e) {

        //  nếu có record thì update failed
        if (isset($anhTrain)) {
            $anhTrain->update(['trang_thai' => 'failed']);
        }

        $raw = $e->getMessage();

        // map lỗi
        if (str_contains($raw, 'cURL error 6')) {
            $msg = 'Không thể kết nối tới server';
        } elseif (str_contains($raw, 'cURL error 28')) {
            $msg = 'Server phản hồi chậm, vui lòng thử lại';
        } else {
            $msg = 'Có lỗi xảy ra, vui lòng thử lại';
        }

        \Log::error($raw);

        return response()->json([
            'success' => false,
            'message' => $msg
        ], 500);
    }
}

    // COMPARE (CALL LAMBDA)
    public function compareMany(Request $request, LichThi $lichThi)
    {
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
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

    // CONFIRM ĐIỂM DANH
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

            if (!$sv) {
                $messages[] = "Không tìm thấy sinh viên {$face['name']}";
                continue;
            }

            // kiểm tra sinh viên có trong phòng thi
            $exists = DiemDanh::where('sinh_vien_id', $sv->id)
                ->where('lich_thi_id', $lichThi->id)
                ->exists();

           if (!$exists) {
                // tìm lịch thi khác trong cùng ngày
                $lichKhacs = DiemDanh::where('sinh_vien_id', $sv->id)
                    ->whereHas('lichThi', function ($q) use ($lichThi) {
                        $q->whereDate('ngay_thi', $lichThi->ngay_thi);
                    })
                    ->with('lichThi.monHoc')
                    ->get();


               if ($lichKhacs->count()) {
                    $info = $lichKhacs->map(function ($item) {
                        $lt = $item->lichThi;
                        return "{$lt->monHoc->ten_mon} - {$lt->phong} - " . $lt->thoi_gian_thi->format('H:i');
                    })->implode(' | ');

                    $messages[] = "⚠️ {$sv->ma_sv} - {$sv->ho_ten} không thuộc phòng này. 👉 Lịch trong ngày: $info";
                }else {
                    $messages[] = "🚫 {$sv->ma_sv} - {$sv->ho_ten} không có lịch thi trong ngày này";
                }

                $faces[$idx]['valid'] = false;
                continue;
            }

            DiemDanh::where('sinh_vien_id', $sv->id)
                ->where('lich_thi_id', $lichThi->id)
                ->update([
                    'ket_qua' => 'hợp lệ',
                    'do_chinh_xac' => $face['similarity'],
                    'thoi_gian_dd' => now(),
                    'hinh_thuc_dd' => 'Camera',
                    'updated_at' => now(),
                ]);

            $sv->increment('so_lan_nhan_dien');

            $soLan = $sv->so_lan_nhan_dien;
            $moi = (float) ($face['similarity'] ?? 0);

            if ($soLan === 1) {
                // LẦN NHẬN DIỆN ĐẦU TIÊN
                $sv->do_chinh_xac_tb = round($moi, 2);
            } else {
                $cu = (float) $sv->do_chinh_xac_tb;

                $sv->do_chinh_xac_tb = round(
                    (($cu * ($soLan - 1)) + $moi) / $soLan,
                    2
                );
            }
            
            $sv->save();
            $faces[$idx]['checkedIn'] = true;
            $faces[$idx]['color'] = 'yellow';
            $faces[$idx]['ho_ten'] = $sv->ho_ten;
            $messages[] = "🎉 Điểm danh thành công sinh viên {$face['name']} - {$sv->ho_ten}";
        }

        session(["faces_{$lichThi->id}" => $faces]);

        return response()->json([
            'message' => implode("\n", $messages),
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
    public function destroyAnhTrain($id)
    {
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        $anh = AnhTrainSv::findOrFail($id); 

        // Gọi Lambda với thông tin cụ thể
        $response = Http::post(env('LAMBDA_DELETE_URL'), [
            'collectionId' => $this->collection,
            'bucket'       => $this->bucket,
            'faceIds'      => json_decode($anh->face_id, true),
            's3Keys'       => [$anh->hinh_anh], 
        ]);

        if ($response->ok()) {
            $anh->delete(); 
            return redirect()->back()->with('success', 'Đã xóa ảnh!');
        }
        
        return redirect()->back()->with('error', 'Lỗi AWS: ' . $response->body());
    }
}
