<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SinhVien;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use App\Models\DiemDanh;
use App\Models\AnhTrainSv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SinhVienControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): GiangVien
    {
        return GiangVien::create([
            'ma_gv' => 'GV001',
            'ho_ten' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'vai_tro' => 'admin',
            'is_active' => true,
        ]);
    }

    private function createGiangVien(): GiangVien
    {
        return GiangVien::create([
            'ma_gv' => 'GV002',
            'ho_ten' => 'GV Thuong',
            'email' => 'gv@test.com',
            'password' => Hash::make('password'),
            'vai_tro' => 'giang_vien',
            'is_active' => true,
        ]);
    }

    // ===== AUTHORIZATION =====

    public function test_guest_khong_truy_cap_duoc(): void
    {
        $response = $this->get('/admin/sinhvien');
        $response->assertRedirect(route('login'));
    }

    public function test_giang_vien_thuong_khong_truy_cap_duoc(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->actingAs($gv, 'giangvien')->get('/admin/sinhvien');
        $response->assertStatus(403);
    }

    // ===== INDEX =====

    public function test_admin_xem_danh_sach_sinh_vien(): void
    {
        $admin = $this->createAdmin();
        SinhVien::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/sinhvien');

        $response->assertStatus(200);
        $response->assertViewIs('sinhvien.index');
        $response->assertViewHas('sinhviens');
    }

    public function test_tim_kiem_sinh_vien_theo_ma_sv(): void
    {
        $admin = $this->createAdmin();
        SinhVien::factory()->create(['ma_sv' => 'SV12345678']);
        SinhVien::factory()->create(['ma_sv' => 'SV99999999']);

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/sinhvien?ma_sv=SV123');

        $response->assertStatus(200);
    }

    public function test_tim_kiem_sinh_vien_theo_ho_ten(): void
    {
        $admin = $this->createAdmin();
        SinhVien::factory()->create(['ho_ten' => 'Nguyen Van An']);
        SinhVien::factory()->create(['ho_ten' => 'Tran Thi B']);

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/sinhvien?ho_ten=Nguyen');

        $response->assertStatus(200);
    }

    // ===== CREATE =====

    public function test_hien_thi_form_them_sinh_vien(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/sinhvien/create');

        $response->assertStatus(200);
        $response->assertViewIs('sinhvien.create');
    }

    // ===== STORE =====

    public function test_them_sinh_vien_thanh_cong(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/sinhvien', [
            'ma_sv' => 'SV12345678',
            'ho_ten' => 'Nguyen Van A',
            'lop' => 'CNTT1',
            'email' => 'svA@test.com',
        ]);

        $response->assertRedirect(route('sinhvien.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('sinh_viens', ['ma_sv' => 'SV12345678']);
    }

    public function test_them_sinh_vien_ma_sv_trung(): void
    {
        $admin = $this->createAdmin();
        SinhVien::factory()->create(['ma_sv' => 'SV12345678']);

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/sinhvien', [
            'ma_sv' => 'SV12345678',
            'ho_ten' => 'Nguyen Van B',
            'lop' => 'CNTT1',
            'email' => 'svB@test.com',
        ]);

        $response->assertSessionHasErrors('ma_sv');
    }

    public function test_them_sinh_vien_email_trung(): void
    {
        $admin = $this->createAdmin();
        SinhVien::factory()->create(['email' => 'dup@test.com']);

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/sinhvien', [
            'ma_sv' => 'SV00000001',
            'ho_ten' => 'Test',
            'lop' => 'CNTT1',
            'email' => 'dup@test.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_them_sinh_vien_ma_sv_khong_du_10_ky_tu(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/sinhvien', [
            'ma_sv' => 'SV123',
            'ho_ten' => 'Nguyen Van A',
            'lop' => 'CNTT1',
            'email' => 'sv@test.com',
        ]);

        $response->assertSessionHasErrors('ma_sv');
    }

    public function test_them_sinh_vien_thieu_truong_bat_buoc(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/sinhvien', [
            'ma_sv' => '',
            'ho_ten' => '',
            'lop' => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['ma_sv', 'ho_ten', 'lop', 'email']);
    }

    // ===== EDIT =====

    public function test_hien_thi_form_sua_sinh_vien(): void
    {
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create();

        $response = $this->actingAs($admin, 'giangvien')->get("/admin/sinhvien/{$sv->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('sinhvien.edit');
    }

    // ===== UPDATE =====

    public function test_cap_nhat_sinh_vien_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV12345678']);

        $response = $this->actingAs($admin, 'giangvien')->put("/admin/sinhvien/{$sv->id}", [
            'ma_sv' => 'SV12345678',
            'ho_ten' => 'Ho Ten Moi',
            'lop' => 'CNTT2',
            'email' => 'newemail@test.com',
        ]);

        $response->assertRedirect(route('sinhvien.index'));
        $this->assertDatabaseHas('sinh_viens', ['ho_ten' => 'Ho Ten Moi', 'lop' => 'CNTT2']);
    }

    public function test_cap_nhat_cho_phep_giu_nguyen_ma_sv(): void
    {
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV12345678', 'email' => 'old@test.com']);

        $response = $this->actingAs($admin, 'giangvien')->put("/admin/sinhvien/{$sv->id}", [
            'ma_sv' => 'SV12345678',
            'ho_ten' => 'Updated Name',
            'lop' => 'CNTT1',
            'email' => 'old@test.com',
        ]);

        $response->assertRedirect(route('sinhvien.index'));
        $response->assertSessionHas('success');
    }

    // ===== DELETE =====

    public function test_xoa_sinh_vien_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create();

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/sinhvien/{$sv->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('sinh_viens', ['id' => $sv->id]);
    }

    public function test_xoa_sinh_vien_co_diem_danh_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create();
        $monHoc = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Test']);
        $year = now()->year;
        $lichThi = LichThi::create([
            'mon_hoc_id' => $monHoc->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'Giữa kỳ',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);
        DiemDanh::create([
            'lich_thi_id' => $lichThi->id,
            'sinh_vien_id' => $sv->id,
        ]);

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/sinhvien/{$sv->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('sinh_viens', ['id' => $sv->id]);
    }

    public function test_xoa_sinh_vien_co_anh_train_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $sv = SinhVien::factory()->create();
        AnhTrainSv::create([
            'sinh_vien_id' => $sv->id,
            'hinh_anh' => 'test.jpg',
            'face_id' => json_encode(['face-1']),
            'file_hash' => md5('test'),
            'file_name' => 'test.jpg',
            'trang_thai' => 'trained',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/sinhvien/{$sv->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('sinh_viens', ['id' => $sv->id]);
    }
}
