<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use App\Models\SinhVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use App\Models\DiemDanh;
use App\Models\AnhTrainSv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RekognitionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): GiangVien
    {
        return GiangVien::factory()->admin()->create();
    }

    private function createGV(): GiangVien
    {
        return GiangVien::factory()->create(['vai_tro' => 'giang_vien']);
    }

    private function createLichThi(): LichThi
    {
        $mh = MonHoc::factory()->create();
        return LichThi::factory()->create(['mon_hoc_id' => $mh->id]);
    }

    // ===== UPLOAD FORM =====

    public function test_admin_truy_cap_form_upload_anh_train(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->get(route('rekognition.uploadForm'));

        $response->assertStatus(200);
        $response->assertViewIs('rekognition.train');
    }

    public function test_giang_vien_thuong_khong_truy_cap_duoc_upload_form(): void
    {
        $gv = $this->createGV();

        $response = $this->actingAs($gv, 'giangvien')->get(route('rekognition.uploadForm'));

        $response->assertStatus(403);
    }

    // ===== INDEX DIEM DANH VIEW =====

    public function test_xem_trang_diem_danh_theo_lich_thi(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create();
        DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $response = $this->actingAs($gv, 'giangvien')->get(route('rekognition.index', $lt->id));

        $response->assertStatus(200);
        $response->assertViewIs('rekognition.index');
        $response->assertViewHas('sinhViens', function ($sinhViens) use ($sv) {
            return $sinhViens->contains('sinh_vien_id', $sv->id);
        });
    }

    public function test_loc_sinh_vien_chua_diem_danh(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $sv1 = SinhVien::factory()->create();
        $sv2 = SinhVien::factory()->create();

        // sv1 hợp lệ, sv2 chưa điểm danh
        DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv1->id,
            'ket_qua' => 'hợp lệ',
        ]);
        DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv2->id,
            'ket_qua' => null,
        ]);

        $response = $this->actingAs($gv, 'giangvien')->get(route('rekognition.index', [
            'lichThi' => $lt->id,
            'chua_diem_danh' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('sinhViens', function ($sinhViens) use ($sv1, $sv2) {
            $ids = $sinhViens->pluck('sinh_vien_id')->all();
            return in_array($sv2->id, $ids) && !in_array($sv1->id, $ids);
        });
    }

    // ===== TRAIN AJAX =====

    public function test_train_validation_that_bai_khi_thieu_truong(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('rekognition.train.ajax'), []);

        // ValidationException được ném ra hoặc trả về lỗi
        $response->assertStatus(500);
    }

    public function test_train_that_bai_khi_sinh_vien_khong_ton_tai(): void
    {
        Storage::fake('s3');
        $admin = $this->createAdmin();
        $file = UploadedFile::fake()->image('face.jpg', 200, 200);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('rekognition.train.ajax'), [
            'ma_sv' => 'SV_KHONG_CO',
            'hinh_anh' => $file,
        ]);

        $response->assertJson([
            'success' => false,
            'message' => 'Không tồn tại MSSV: SV_KHONG_CO',
        ]);
    }

    public function test_train_that_bai_khi_anh_da_duoc_upload_truoc_do(): void
    {
        Storage::fake('s3');
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV12345678']);
        $file = UploadedFile::fake()->image('face.jpg', 200, 200);

        $fileHash = md5($file->getContent());
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'file_hash' => $fileHash,
            'file_name' => 'face.jpg',
            'trang_thai' => 'trained',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('rekognition.train.ajax'), [
            'ma_sv' => $sv->ma_sv,
            'hinh_anh' => $file,
        ]);

        $response->assertJson([
            'success' => false,
            'message' => 'Ảnh này đã được upload trước đó',
        ]);
    }

    public function test_train_that_bai_khi_qua_gioi_han_2_anh(): void
    {
        Storage::fake('s3');
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV12345678']);

        // Đã có 2 ảnh trained
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'file_hash' => md5('image1'),
            'file_name' => 'img1.jpg',
            'trang_thai' => 'trained',
        ]);
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'file_hash' => md5('image2'),
            'file_name' => 'img2.jpg',
            'trang_thai' => 'trained',
        ]);

        $file = UploadedFile::fake()->image('face3.jpg', 200, 200);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('rekognition.train.ajax'), [
            'ma_sv' => $sv->ma_sv,
            'hinh_anh' => $file,
        ]);

        $response->assertJson([
            'success' => false,
            'message' => 'Sinh viên đã đạt giới hạn 2 ảnh',
        ]);
    }

    public function test_train_thanh_cong_qua_mock_lambda(): void
    {
        Storage::fake('s3');
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV12345678']);
        $file = UploadedFile::fake()->image('face.jpg', 200, 200);

        Http::fake([
            '*' => Http::response([
                'success' => true,
                'face_ids' => ['face-uuid-001'],
                'image_key' => 'trained/SV12345678_001.jpg',
            ], 200),
        ]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('rekognition.train.ajax'), [
            'ma_sv' => $sv->ma_sv,
            'hinh_anh' => $file,
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Tải ảnh và train thành công',
        ]);

        $this->assertDatabaseHas('anh_train_svs', [
            'sinh_vien_id' => $sv->id,
            'trang_thai' => 'trained',
            'hinh_anh' => 'trained/SV12345678_001.jpg',
        ]);
    }

    public function test_train_that_bai_khi_lambda_loi(): void
    {
        Storage::fake('s3');
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV12345678']);
        $file = UploadedFile::fake()->image('face.jpg', 200, 200);

        Http::fake([
            '*' => Http::response('Server Error', 500),
        ]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('rekognition.train.ajax'), [
            'ma_sv' => $sv->ma_sv,
            'hinh_anh' => $file,
        ]);

        $response->assertJson([
            'success' => false,
            'message' => 'Không gọi được Lambda',
        ]);

        $this->assertDatabaseHas('anh_train_svs', [
            'sinh_vien_id' => $sv->id,
            'trang_thai' => 'failed',
        ]);
    }

    // ===== COMPARE MANY =====

    public function test_compare_many_thanh_cong_luu_session(): void
    {
        Storage::fake('s3');
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV99999999', 'ho_ten' => 'Test Compare']);
        DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $fakeBase64 = 'data:image/jpeg;base64,' . base64_encode('fake-image-binary-data');

        Http::fake([
            '*' => Http::response([
                'attendees' => [
                    [
                        'externalImageId' => 'SV99999999',
                        'similarity' => 95.5,
                        'box' => ['left' => 10, 'top' => 10, 'width' => 50, 'height' => 50],
                    ],
                ],
                'unknownFaces' => [
                    [
                        'reason' => 'Không tìm thấy khuôn mặt trong hệ thống',
                        'box' => ['left' => 70, 'top' => 70, 'width' => 40, 'height' => 40],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($gv, 'giangvien')->postJson(route('rekognition.compareMany', $lt->id), [
            'hinh_anh_base64' => $fakeBase64,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['faces']);
        $this->assertCount(2, $response->json('faces'));
        $this->assertEquals('SV99999999', $response->json('faces.0.name'));
        $this->assertEquals('green', $response->json('faces.0.color'));
        $this->assertEquals('red', $response->json('faces.1.color'));
    }

    // ===== CONFIRM MANY =====

    public function test_confirm_many_diem_danh_thanh_cong_cap_nhat_do_chinh_xac(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create([
            'ma_sv' => 'SV88888888',
            'ho_ten' => 'Sinh Vien Checkin',
            'so_lan_nhan_dien' => 0,
            'do_chinh_xac_tb' => 0,
        ]);
        $dd = DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        // Giả lập session chứa faces từ lần compare trước
        $faces = [
            [
                'name' => 'SV88888888',
                'valid' => true,
                'similarity' => 96.4,
                'box' => ['x' => 10, 'y' => 10, 'width' => 50, 'height' => 50],
                'color' => 'green',
                'checkedIn' => false,
            ],
        ];

        $response = $this->actingAs($gv, 'giangvien')
            ->withSession(["faces_{$lt->id}" => $faces])
            ->postJson(route('rekognition.confirmMany', $lt->id), [
                'faces' => json_encode([0]),
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('Điểm danh thành công', $response->json('message'));

        $dd->refresh();
        $this->assertEquals('hợp lệ', $dd->ket_qua);
        $this->assertEquals(96.4, (float) $dd->do_chinh_xac);
        $this->assertEquals('Camera', $dd->hinh_thuc_dd);

        $sv->refresh();
        $this->assertEquals(1, $sv->so_lan_nhan_dien);
        $this->assertEquals(96.4, (float) $sv->do_chinh_xac_tb);
    }

    public function test_confirm_many_sinh_vien_khong_thuoc_phong_thi(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV77777777']);
        // Không tạo DiemDanh cho ca thi $lt

        $faces = [
            [
                'name' => 'SV77777777',
                'valid' => true,
                'similarity' => 90.0,
                'box' => ['x' => 0, 'y' => 0, 'width' => 10, 'height' => 10],
                'color' => 'green',
                'checkedIn' => false,
            ],
        ];

        $response = $this->actingAs($gv, 'giangvien')
            ->withSession(["faces_{$lt->id}" => $faces])
            ->postJson(route('rekognition.confirmMany', $lt->id), [
                'faces' => json_encode([0]),
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('không có lịch thi trong ngày này', $response->json('message'));
    }

    // ===== GET ATTENDANCE DATA =====

    public function test_lay_du_lieu_bang_diem_danh(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create();
        DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $response = $this->actingAs($gv, 'giangvien')->get(route('rekognition.getAttendanceData', $lt->id));

        $response->assertStatus(200);
        $response->assertViewIs('rekognition.attendance_table');
    }

    // ===== DESTROY ANH TRAIN =====

    public function test_xoa_anh_train_thanh_cong_qua_mock_lambda(): void
    {
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create();
        $anh = AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'hinh_anh' => 'test/img.jpg',
            'face_id' => json_encode(['face-id-123']),
            'file_hash' => md5('del-test'),
            'file_name' => 'img.jpg',
            'trang_thai' => 'trained',
        ]);

        Http::fake([
            '*' => Http::response(['success' => true], 200),
        ]);

        $response = $this->actingAs($admin, 'giangvien')->delete(route('anh_train.destroy', $anh->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('anh_train_svs', ['id' => $anh->id]);
    }
}
