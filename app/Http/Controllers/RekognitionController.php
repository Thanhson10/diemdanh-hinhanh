<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Rekognition\RekognitionClient;
use Aws\S3\S3Client;
use Aws\Rekognition\Exception\RekognitionException;
use App\Models\SinhVien;
use App\Models\LichThi;
use App\Models\DiemDanh;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\ValidationException;
class RekognitionController extends Controller
{
    private $rekognition;
    private $s3;
    private $bucket = 'diemdanh-sinhvien'; 
    private $collection = 'sinhvien_faces';

    public function __construct()
    {
        $this->rekognition = new RekognitionClient([
            'region' => 'ap-southeast-1',
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $this->s3 = new S3Client([
            'region' => 'ap-southeast-1',
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }
    public function uploadForm()
    {
        return view('rekognition.train',['hideSearch' => true]);
    }
    //Hiển thị form điểm danh
   public function index($lichThi)
    {
        // $lichThi là id truyền từ route, tìm record tương ứng
        $lichThi = LichThi::findOrFail($lichThi);
        
        // Lấy danh sách sinh viên với các điều kiện lọc
        $query = DiemDanh::with('sinhVien')
            ->where('lich_thi_id', $lichThi->id);

        // Lọc theo tìm kiếm
        if (request('search')) {
            $search = request('search');
            $query->whereHas('sinhVien', function($q) use ($search) {
                $q->where('ma_sv', 'like', "%{$search}%")
                ->orWhere('ho_ten', 'like', "%{$search}%");
            });
        }

        // Lọc chưa điểm danh
        if (request('chua_diem_danh')) {
            $query->where(function($q) {
                $q->where('ket_qua', '!=', 'hợp lệ')
                ->orWhereNull('ket_qua');
            });
        }

        $sinhViens = $query->get();

        // truyền cả 2 biến $lichThi và $sinhViens vào view
        return view('rekognition.index', compact('lichThi', 'sinhViens'));
    }

    // Tạo collection (chỉ cần chạy 1 lần)
    public function createCollection()
    {
        try {
            $result = $this->rekognition->createCollection([
                'CollectionId' => $this->collection,
            ]);
            return response()->json(['success' => true, 'message' => 'Đã tạo collection Rekognition thành công!', 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function deleteStudent($studentId)
    {
        try {
            $rekognition = new \Aws\Rekognition\RekognitionClient([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $s3 = new \Aws\S3\S3Client([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $collectionId = 'sinhvien_faces';
            $bucket = env('AWS_BUCKET');
            $imageKey = "sinhvien/{$studentId}.jpg";

            // 🧠 1. Tìm faceId trong Rekognition (theo ExternalImageId)
            $faces = $rekognition->listFaces([
                'CollectionId' => $collectionId,
            ]);

            $faceIds = [];
            foreach ($faces['Faces'] as $face) {
                if ($face['ExternalImageId'] === $studentId) {
                    $faceIds[] = $face['FaceId'];
                }
            }

            // ❗ Nếu không tìm thấy face nào
            if (empty($faceIds)) {
                // Kiểm tra xem ảnh có tồn tại trên S3 không
                $exists = false;
                try {
                    $s3->headObject([
                        'Bucket' => $bucket,
                        'Key' => $imageKey,
                    ]);
                    $exists = true;
                } catch (\Aws\S3\Exception\S3Exception $e) {
                    $exists = false;
                }

                if (!$exists) {
                    return redirect()->back()->with('error', "Không tìm thấy dữ liệu sinh viên {$studentId} trong Rekognition hoặc S3!");
                }
            }

            // 🧽 2. Xóa khuôn mặt khỏi Rekognition
            if (!empty($faceIds)) {
                $rekognition->deleteFaces([
                    'CollectionId' => $collectionId,
                    'FaceIds' => $faceIds,
                ]);
            }

            // 🧹 3. Xóa ảnh khỏi S3
            $s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $imageKey,
            ]);

            return redirect()->back()->with('success', "✅ Đã xóa sinh viên {$studentId} khỏi Rekognition và S3!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Lỗi khi xóa: ' . $e->getMessage());
        }
    }

    public function trainAjax(Request $request)
    {
        $request->merge(['force_retrain' => 0]);
        return $this->handleTrain($request);
    }
    public function retrainAjax(Request $request)
    {
        $request->merge(['force_retrain' => 1]);
        return $this->handleTrain($request);
    }

    private function handleTrain(Request $request)
    {   
        try {
            $request->validate([
            'ma_sv' => 'required',
            'hinh_anh' => 'required|mimes:jpg,jpeg,png|max:5120',
            ],[
        'hinh_anh.required' => 'Chưa chọn ảnh',
        'hinh_anh.image' => 'File không phải là ảnh hợp lệ',
        'hinh_anh.mimes' => 'Ảnh phải là JPG hoặc PNG',
        'hinh_anh.max' => 'Ảnh vượt quá 5MB',
        'hinh_anh.uploaded' => 'Upload ảnh thất bại',
        ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['hinh_anh'][0] ?? 'Dữ liệu không hợp lệ'
            ], 422);
        }
        
        try {
           
            $ma_sv = strtoupper(trim($request->ma_sv));
            $image = $request->file('hinh_anh');
            $force = $request->boolean('force_retrain');

            // 1️⃣ kiểm tra sinh viên
            $sv = SinhVien::where('ma_sv', $ma_sv)->first();
            if (!$sv) {
                return response()->json([
                    'success' => false,
                    'message' => "Không tồn tại MSSV: $ma_sv"
                ]);
            }

            // 2️⃣ đã train nhưng không cho train lại
            if ($sv->da_train_khuon_mat && !$force) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sinh viên đã có mẫu khuôn mặt'
                ]);
            }

            // 3️⃣ retrain
            if ($force) {

                // 🚫 Chưa từng train mà bấm "train lại"
                if (!$sv->da_train_khuon_mat) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sinh viên chưa có mẫu khuôn mặt'
                    ]);
                }

                // 🚫 Đã có mẫu nhưng chưa đủ điều kiện retrain
                if (!$sv->canRetrain()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Chưa đủ điều kiện train lại'
                    ]);
                }
            }

            // 4️⃣ UPLOAD ẢNH TẠM
            $manager = new ImageManager(new Driver());

            $imageJpg = $manager
                ->read($image->getPathname())
                ->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->toJpeg(90);

            $tempPath = "sinhvien/tmp/{$ma_sv}_" . uniqid() . ".jpg";
            $finalPath = "sinhvien/{$ma_sv}.jpg";

            Storage::disk('s3')->put($tempPath, (string) $imageJpg, 'public');

            // 5️⃣ INDEX FACE (DÙNG ẢNH TẠM)
            $result = $this->rekognition->indexFaces([
                'CollectionId' => $this->collection,
                'Image' => [
                    'S3Object' => [
                        'Bucket' => $this->bucket,
                        'Name' => $tempPath,
                    ],
                ],
                'ExternalImageId' => $ma_sv,
                'MaxFaces' => 5,
                'QualityFilter' => 'AUTO',
                'DetectionAttributes' => ['DEFAULT'],
            ]);

            $detectedFaces = count($result['FaceRecords'] ?? []);

            // 6️⃣ FAIL → XÓA ẢNH TẠM → RETURN
            if ($detectedFaces === 0) {
                Storage::disk('s3')->delete($tempPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Không phát hiện khuôn mặt trong ảnh'
                ]);
            }

            if ($detectedFaces > 1) {
                Storage::disk('s3')->delete($tempPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Ảnh có nhiều hơn 1 khuôn mặt',
                    'detected_faces' => $detectedFaces
                ]);
            }

            // 7️⃣ COMMIT – XÓA DỮ LIỆU CŨ
            if ($force && !empty($sv->face_ids)) {
                $this->rekognition->deleteFaces([
                    'CollectionId' => $this->collection,
                    'FaceIds' => $sv->face_ids,
                ]);

                Storage::disk('s3')->delete($finalPath);
            }

            // 8️⃣ MOVE ẢNH TẠM → ẢNH CHÍNH
            Storage::disk('s3')->move($tempPath, $finalPath);

            // 9️⃣ LƯU DB
            $faceIds = collect($result['FaceRecords'])
                ->pluck('Face.FaceId')
                ->toArray();

            $sv->update([
                'da_train_khuon_mat' => true,
                'face_ids' => $faceIds,
                'do_chinh_xac_tb' => null,
                'so_lan_nhan_dien' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => $force ? 'Train lại thành công' : 'Train lần đầu thành công',
                'face_count' => count($faceIds),
            ]);

        } catch (\Throwable $e) {
            \Log::error('Train thất bại', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống khi train khuôn mặt'
            ], 500);
        }
    }

    public function confirmMany(Request $request, LichThi $lichThi)
    {
        $request->validate([
            'faces' => 'required|json', // danh sách index được chọn
        ]);

        $faceIndexes = json_decode($request->faces, true);
        $messages = [];

        // ✅ Lấy dữ liệu face từ session
        $faces = session("faces_{$lichThi->id}", []);
        if (empty($faces)) {
            return response()->json(['message' => 'Không có dữ liệu nhận diện, vui lòng chụp lại'], 400);
        }

        foreach ($faceIndexes as $idx) {
            if (!isset($faces[$idx])) continue;

            $face = $faces[$idx];

            // Kiểm tra nếu đã điểm danh rồi thì bỏ qua
            if ($face['checkedIn'] ?? false) {
                $messages[] = "Sinh viên {$face['name']} đã được điểm danh trước đó";
                continue;
            }

            if (!$face['valid'] || empty($face['name'])) {
                $messages[] = "Face #{$idx} không hợp lệ";
                continue;
            }

            $sinhVien = SinhVien::where('ma_sv', $face['name'])->first();
            if (!$sinhVien) {
                $messages[] = "Không tìm thấy sinh viên {$face['name']}";
                continue;
            }

            // ✅ kiểm tra sinh viên có trong phòng thi
            $exists = DiemDanh::where('sinh_vien_id', $sinhVien->id)
                ->where('lich_thi_id', $lichThi->id)
                ->exists();

            if (!$exists) {
                $messages[] = "Sinh viên {$face['name']} không thuộc phòng thi {$lichThi->phong}";
                // Đánh dấu là không hợp lệ trong session
                $faces[$idx]['valid'] = false;
                continue;
            }

            // ✅ cập nhật điểm danh
            DiemDanh::where('sinh_vien_id', $sinhVien->id)
                ->where('lich_thi_id', $lichThi->id)
                ->update([
                    'ket_qua' => 'hợp lệ',
                    'do_chinh_xac' => $face['similarity'],
                    'thoi_gian_dd' => now(),
                    'hinh_thuc_dd' => 'Camera',
                    'updated_at' => now(),
                ]);
                
            $sinhVien->increment('so_lan_nhan_dien');

            $soLan = $sinhVien->so_lan_nhan_dien;
            $moi = (float) ($face['similarity'] ?? 0);

            if ($soLan === 1) {
                // LẦN NHẬN DIỆN ĐẦU TIÊN
                $sinhVien->do_chinh_xac_tb = round($moi, 2);
            } else {
                $cu = (float) $sinhVien->do_chinh_xac_tb;

                $sinhVien->do_chinh_xac_tb = round(
                    (($cu * ($soLan - 1)) + $moi) / $soLan,
                    2
                );
            }

            $sinhVien->save();

            // Cập nhật trạng thái trong session
            $faces[$idx]['checkedIn'] = true;
            $faces[$idx]['ho_ten'] = $sinhVien->ho_ten;
            
            $messages[] = "🎉 Điểm danh thành công sinh viên {$face['name']} - {$sinhVien->ho_ten}";
        }

        // Cập nhật session với trạng thái mới
        session(["faces_{$lichThi->id}" => $faces]);

        return response()->json([
            'message' => implode("\n", $messages),
            'updated_faces' => $faces // Gửi lại dữ liệu faces đã cập nhật
        ]);
    }

    /**
     * Quyết định có nên search khuôn mặt hay không (giảm chi phí Rekognition)
     */
    protected function shouldSearchFace(array $fd, int $imgW, int $imgH): array
    {
        // Ngưỡng cơ bản
        $MIN_CONFIDENCE = 95;
        $MIN_FACE_RATIO = 0.03;   // >= 3% diện tích ảnh
        $MIN_BRIGHTNESS = 25;
        $MAX_YAW        = 25;
        $MAX_PITCH      = 20;

        // ---- Lấy dữ liệu ----
        $confidence = $fd['Confidence'] ?? 0;
        $box        = $fd['BoundingBox'] ?? null;
        $quality    = $fd['Quality'] ?? [];
        $pose       = $fd['Pose'] ?? [];

        if (!$box) {
            return [false, 'Missing BoundingBox'];
        }

        $width  = $box['Width'];
        $height = $box['Height'];
        $area   = $width * $height;

        $sharpness  = $quality['Sharpness']  ?? 0;
        $brightness = $quality['Brightness'] ?? 0;

        // ---- 1️⃣ Confidence ----
        if ($confidence < $MIN_CONFIDENCE) {
            return [false, "Low confidence ($confidence)"];
        }

        // ---- 2️⃣ Khuôn mặt quá nhỏ ----
        if ($area < $MIN_FACE_RATIO) {
            return [false, 'Face too small'];
        }

        // ---- 3️⃣ Sharpness động theo kích thước ----
        if ($area >= 0.05) {
            $minSharpness = 6;
        } elseif ($area >= 0.04) {
            $minSharpness = 8;
        } else { // 0.03 – 0.04
            $minSharpness = 10;
        }

        if ($sharpness < $minSharpness) {
            return [false, "Face too blurry (sharpness=$sharpness)"];
        }

        // ---- 4️⃣ Ánh sáng ----
        if ($brightness < $MIN_BRIGHTNESS) {
            return [false, "Too dark (brightness=$brightness)"];
        }

        // ---- 5️⃣ Góc mặt ----
        if (abs($pose['Yaw'] ?? 0) > $MAX_YAW) {
            return [false, 'Yaw too large'];
        }

        if (abs($pose['Pitch'] ?? 0) > $MAX_PITCH) {
            return [false, 'Pitch too large'];
        }

        // ---- 6️⃣ Bị che ----
        if (($fd['Occluded']['Value'] ?? false) === true) {
            return [false, 'Face occluded'];
        }

        // ✅ Qua tất cả filter → mới gọi searchFacesByImage
        return [true, 'OK'];
    }


    public function compareMany(Request $request, LichThi $lichThi)
    {
        $request->validate([
            'hinh_anh_base64' => 'required',
        ]);

        // --- Nhận base64
        $dataUrl = $request->hinh_anh_base64;
        if (empty($dataUrl)) {
            return response()->json(['error' => true, 'message' => 'Không nhận được hình ảnh'], 400);
        }

        // Tách data:image/...;base64,
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $dataUrl);
        $imageBytes = base64_decode($base64, true);

        if ($imageBytes === false || strlen($imageBytes) < 10) {
            return response()->json([
                'error' => true,
                'message' => 'Base64 decode thất bại hoặc ảnh quá nhỏ'
            ], 400);
        }

        // --- Chuẩn hóa orientation
        try {
            $normalizedBytes = $this->normalizeImageOrientation($imageBytes);
        } catch (\Exception $e) {
            $normalizedBytes = $imageBytes; // fallback
        }

        // Tạo GD image để lấy kích thước
        $source = @imagecreatefromstring($normalizedBytes);
        if (!$source) {
            return response()->json(['error' => true, 'message' => 'Không thể đọc ảnh sau normalize'], 400);
        }
        $origWidth = imagesx($source);
        $origHeight = imagesy($source);
        imagedestroy($source);

        // --- DetectFaces
        try {
            $detect = $this->rekognition->detectFaces([
                'Image' => ['Bytes' => $normalizedBytes],
                'Attributes' => ['DEFAULT'],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Rekognition detectFaces lỗi: ' . $e->getMessage(),
            ], 500);
        }

        $faceDetails = $detect['FaceDetails'] ?? [];
        if (empty($faceDetails)) {
            return response()->json([
                'error' => false,
                'faces' => [],
                'message' => 'Không phát hiện khuôn mặt',
            ], 200);
        }

        // --- Build face list
        $faces = [];
        foreach ($faceDetails as $fd) {
            $box = $fd['BoundingBox'];

            $faces[] = [
                'box' => [
                    'x' => (float)($box['Left'] * $origWidth),
                    'y' => (float)($box['Top'] * $origHeight),
                    'width' => (float)($box['Width'] * $origWidth),
                    'height' => (float)($box['Height'] * $origHeight),
                ],
                'faceDetail' => $fd,
                'name' => null,            
                'ho_ten' => null,          
                'similarity' => null,
                'valid' => false,
                'checkedIn' => false,
                'search_error' => null,
            ];
        }

        // --- Crop + Search
        foreach ($faces as $i => &$face) {
        // 🔍 LỌC KHUÔN MẶT TRƯỚC
        [$allowSearch, $reason] = $this->shouldSearchFace(
            $face['faceDetail'],
            $origWidth,
            $origHeight
        );

        if (!$allowSearch) {
            $face['search_error'] = "Skipped before search: $reason";
            continue; // ❌ KHÔNG gọi searchFacesByImage
        }

        // --- Crop
        try {
            $cropBytes = $this->cropFaceForSearch($normalizedBytes, $face['box']);
        } catch (\Exception $e) {
            $face['search_error'] = 'Crop failed: ' . $e->getMessage();
            continue;
        }

        // --- Kiểm tra kích thước crop
        try {
            $tmp = imagecreatefromstring($cropBytes);
            $cw = imagesx($tmp);
            $ch = imagesy($tmp);
            imagedestroy($tmp);

            if ($cw < 120 || $ch < 120) {
                $face['search_error'] = "Crop too small ($cw x $ch)";
                continue;
            }
        } catch (\Exception $e) {
            $face['search_error'] = 'Inspect crop failed';
            continue;
        }

        // 🔥 CHỈ ĐẾN ĐÂY MỚI GỌI REKOGNITION SEARCH
        try {
            $search = $this->rekognition->searchFacesByImage([
                'CollectionId' => $this->collection,
                'Image' => ['Bytes' => $cropBytes],
                'FaceMatchThreshold' => 90,
                'MaxFaces' => 1,
            ]);
        } catch (\Exception $e) {
            $face['search_error'] = 'AWS search error: ' . $e->getMessage();
            continue;
        }

        if (!empty($search['FaceMatches'])) {
            $ma_sv = $search['FaceMatches'][0]['Face']['ExternalImageId'] ?? null;
            $similarity = $search['FaceMatches'][0]['Similarity'] ?? null;

            $sinhVien = $ma_sv ? SinhVien::where('ma_sv', $ma_sv)->first() : null;

            $face['name'] = $sinhVien ? $sinhVien->ma_sv : $ma_sv;
            $face['ho_ten'] = $sinhVien ? $sinhVien->ho_ten : null;
            $face['similarity'] = $similarity;
            $face['valid'] = (bool)$sinhVien;
        } else {
            $face['search_error'] = 'No FaceMatches';
        }
    }

        // Lưu session
        session(["faces_{$lichThi->id}" => $faces]);

        return response()->json([
            'error' => false,
            'faces' => $faces
        ], 200);
    }

    /**
     * Chuẩn hoá orientation cho ảnh JPEG (nếu có EXIF Orientation).
     */
    protected function normalizeImageOrientation($imageBytes)
    {
        // lưu tạm file vì exif cần path
        $tmp = sys_get_temp_dir() . '/rek_norm_' . uniqid() . '.jpg';
        file_put_contents($tmp, $imageBytes);

        $exif = @exif_read_data($tmp);
        @unlink($tmp);

        $orientation = $exif['Orientation'] ?? 1;

        $img = @imagecreatefromstring($imageBytes);
        if (!$img) {
            throw new \Exception('Không thể tạo image từ bytes (normalize)');
        }

        switch ($orientation) {
            case 3: $img = imagerotate($img, 180, 0); break;
            case 6: $img = imagerotate($img, -90, 0); break;
            case 8: $img = imagerotate($img, 90, 0); break;
        }

        ob_start();
        imagejpeg($img, null, 90);
        $out = ob_get_clean();
        imagedestroy($img);

        return $out;
    }


    /**
     * Phiên bản cropFace cho search: nhận box pixel, mở rộng padding, trả crop bytes JPEG.
     * Ném Exception nếu crop quá nhỏ hoặc invalid.
    */
    protected function cropFaceForSearch($imageBytes, $box)
    {
        $src = imagecreatefromstring($imageBytes);
        if (!$src) {
            throw new \Exception('Không thể tạo GD ảnh trong cropFaceForSearch');
        }

        $W = imagesx($src);
        $H = imagesy($src);

        $x = (int)$box['x'];
        $y = (int)$box['y'];
        $w = (int)$box['width'];
        $h = (int)$box['height'];

        // padding 25%
        $padX = (int)($w * 0.25);
        $padY = (int)($h * 0.25);

        $x = max(0, $x - $padX);
        $y = max(0, $y - $padY);
        $w = min($W - $x, $w + $padX * 2);
        $h = min($H - $y, $h + $padY * 2);

        if ($w <= 0 || $h <= 0) {
            imagedestroy($src);
            throw new \Exception("Crop invalid ($w x $h)");
        }

        $dst = imagecreatetruecolor($w, $h);
        imagecopy($dst, $src, 0, 0, $x, $y, $w, $h);

        ob_start();
        imagejpeg($dst, null, 90);
        $out = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $out;
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
}
