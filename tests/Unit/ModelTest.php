<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SinhVien;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use App\Models\DiemDanh;
use App\Models\PhanCongGV;
use App\Models\AnhTrainSv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    // ===== HELPER =====

    private function createGV(array $overrides = []): GiangVien
    {
        static $counter = 0;
        $counter++;
        return GiangVien::create(array_merge([
            'ma_gv' => 'GV' . str_pad($counter, 4, '0', STR_PAD_LEFT),
            'ho_ten' => 'GV ' . $counter,
            'email' => "gv{$counter}@test.com",
            'password' => Hash::make('password'),
            'vai_tro' => 'giang_vien',
            'is_active' => true,
        ], $overrides));
    }

    private function createLichThi(): LichThi
    {
        $mh = MonHoc::create(['ma_mon' => 'MH' . uniqid(), 'ten_mon' => 'Test']);
        $year = now()->year;
        return LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P' . rand(100, 999),
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);
    }

    // ===== SINH VIEN =====

    public function test_sinh_vien_has_many_diem_danh(): void
    {
        $sv = SinhVien::factory()->create();
        $lt = $this->createLichThi();
        DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $this->assertCount(1, $sv->diemDanhs);
    }

    public function test_sinh_vien_belongs_to_many_lich_thi(): void
    {
        $sv = SinhVien::factory()->create();
        $lt = $this->createLichThi();
        DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $this->assertCount(1, $sv->lichThis);
        $this->assertEquals($lt->id, $sv->lichThis->first()->id);
    }

    public function test_sinh_vien_can_retrain_true(): void
    {
        $sv = SinhVien::factory()->create([
            'so_lan_nhan_dien' => 10,
            'do_chinh_xac_tb' => 70.0,
        ]);

        $this->assertTrue($sv->canRetrain());
    }

    public function test_sinh_vien_can_retrain_false_khi_chinh_xac_cao(): void
    {
        $sv = SinhVien::factory()->create([
            'so_lan_nhan_dien' => 10,
            'do_chinh_xac_tb' => 90.0,
        ]);

        $this->assertFalse($sv->canRetrain());
    }

    public function test_sinh_vien_can_retrain_false_khi_it_lan_nhan_dien(): void
    {
        $sv = SinhVien::factory()->create([
            'so_lan_nhan_dien' => 3,
            'do_chinh_xac_tb' => 70.0,
        ]);

        $this->assertFalse($sv->canRetrain());
    }

    public function test_sinh_vien_has_many_anh_train(): void
    {
        $sv = SinhVien::factory()->create();
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'hinh_anh' => 'test.jpg',
            'face_id' => json_encode(['face-1']),
            'file_hash' => md5('test1'),
            'file_name' => 'test1.jpg',
            'trang_thai' => 'trained',
        ]);
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'hinh_anh' => null,
            'face_id' => null,
            'file_hash' => md5('test2'),
            'file_name' => 'test2.jpg',
            'trang_thai' => 'failed',
        ]);

        $this->assertCount(2, $sv->danhSachAnhTrain);
        $this->assertCount(1, $sv->anhDaTrain);
    }

    // ===== GIANG VIEN =====

    public function test_giang_vien_has_many_phan_cong(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        PhanCongGV::create(['lich_thi_id' => $lt->id, 'giang_vien_id' => $gv->id]);

        $this->assertCount(1, $gv->phanCongGVs);
    }

    public function test_giang_vien_belongs_to_many_lich_thi(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        PhanCongGV::create(['lich_thi_id' => $lt->id, 'giang_vien_id' => $gv->id]);

        $this->assertCount(1, $gv->lichThis);
    }

    // ===== MON HOC =====

    public function test_mon_hoc_has_many_lich_thi(): void
    {
        $mh = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Test']);
        $year = now()->year;
        LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);

        $this->assertCount(1, $mh->lichThi);
    }

    // ===== LICH THI =====

    public function test_lich_thi_belongs_to_mon_hoc(): void
    {
        $lt = $this->createLichThi();

        $this->assertNotNull($lt->monHoc);
        $this->assertInstanceOf(MonHoc::class, $lt->monHoc);
    }

    public function test_lich_thi_thoi_gian_thi_accessor(): void
    {
        $lt = $this->createLichThi();

        $this->assertNotNull($lt->thoi_gian_thi);
        $this->assertInstanceOf(\Carbon\Carbon::class, $lt->thoi_gian_thi);
    }

    public function test_lich_thi_cap_nhat_trang_thai_da_ket_thuc(): void
    {
        $mh = MonHoc::create(['ma_mon' => 'MH_PAST', 'ten_mon' => 'Past']);
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->subDays(1)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'chua_dien_ra',
        ]);

        $lt->capNhatTrangThai();

        $this->assertEquals('da_ket_thuc', $lt->trang_thai);
    }

    public function test_lich_thi_cap_nhat_trang_thai_chua_dien_ra(): void
    {
        $mh = MonHoc::create(['ma_mon' => 'MH_FUTURE', 'ten_mon' => 'Future']);
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P102',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'dang_dien_ra',
        ]);

        $lt->capNhatTrangThai();

        $this->assertEquals('chua_dien_ra', $lt->trang_thai);
    }

    // ===== DIEM DANH =====

    public function test_diem_danh_belongs_to_sinh_vien(): void
    {
        $sv = SinhVien::factory()->create();
        $lt = $this->createLichThi();
        $dd = DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $this->assertNotNull($dd->sinhVien);
        $this->assertEquals($sv->id, $dd->sinhVien->id);
    }

    public function test_diem_danh_belongs_to_lich_thi(): void
    {
        $sv = SinhVien::factory()->create();
        $lt = $this->createLichThi();
        $dd = DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $this->assertNotNull($dd->lichThi);
        $this->assertEquals($lt->id, $dd->lichThi->id);
    }

    public function test_diem_danh_ghi_chu_text_accessor(): void
    {
        $sv = SinhVien::factory()->create();
        $lt = $this->createLichThi();
        $dd = DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv->id,
            'ghi_chu' => 'chua_co_anh',
        ]);

        $this->assertEquals('Chưa có ảnh', $dd->ghi_chu_text);
    }

    public function test_diem_danh_ghi_chu_text_default(): void
    {
        $sv = SinhVien::factory()->create();
        $lt = $this->createLichThi();
        $dd = DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv->id,
            'ghi_chu' => 'something_else',
        ]);

        $this->assertEquals('-', $dd->ghi_chu_text);
    }

    // ===== ANH TRAIN SV =====

    public function test_anh_train_belongs_to_sinh_vien(): void
    {
        $sv = SinhVien::factory()->create();
        $anh = AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'hinh_anh' => 'test.jpg',
            'face_id' => json_encode(['face-1']),
            'file_hash' => md5('unique'),
            'file_name' => 'test.jpg',
            'trang_thai' => 'trained',
        ]);

        $this->assertNotNull($anh->sinhVien);
        $this->assertEquals($sv->id, $anh->sinhVien->id);
    }

    public function test_anh_train_cleanup_failed(): void
    {
        $sv = SinhVien::factory()->create();

        // Old failed record (should be cleaned up)
        $oldFailed = AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'file_hash' => md5('old-failed'),
            'file_name' => 'old.jpg',
            'trang_thai' => 'failed',
        ]);
        $oldFailed->created_at = now()->subDays(5);
        $oldFailed->save();

        // Recent failed record (should NOT be cleaned up)
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'file_hash' => md5('new-failed'),
            'file_name' => 'new.jpg',
            'trang_thai' => 'failed',
        ]);

        // Trained record (should NOT be cleaned up)
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'hinh_anh' => 'trained.jpg',
            'face_id' => json_encode(['face-1']),
            'file_hash' => md5('trained'),
            'file_name' => 'trained.jpg',
            'trang_thai' => 'trained',
        ]);

        AnhTrainSv::cleanupFailed();

        $this->assertEquals(2, AnhTrainSv::count()); // recent failed + trained remain
    }

    // ===== PHAN CONG GV =====

    public function test_phan_cong_belongs_to_giang_vien(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $pc = PhanCongGV::create(['lich_thi_id' => $lt->id, 'giang_vien_id' => $gv->id]);

        $this->assertNotNull($pc->giangVien);
        $this->assertEquals($gv->id, $pc->giangVien->id);
    }

    public function test_phan_cong_belongs_to_lich_thi(): void
    {
        $gv = $this->createGV();
        $lt = $this->createLichThi();
        $pc = PhanCongGV::create(['lich_thi_id' => $lt->id, 'giang_vien_id' => $gv->id]);

        $this->assertNotNull($pc->lichThi);
        $this->assertEquals($lt->id, $pc->lichThi->id);
    }
}
