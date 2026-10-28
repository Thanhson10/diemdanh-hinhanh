<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MonHocControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): GiangVien
    {
        return GiangVien::create([
            'ma_gv' => 'ADMIN01',
            'ho_ten' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'vai_tro' => 'admin',
            'is_active' => true,
        ]);
    }

    // ===== INDEX =====

    public function test_admin_xem_danh_sach_mon_hoc(): void
    {
        $admin = $this->createAdmin();
        MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Lap trinh C']);

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/monhoc');

        $response->assertStatus(200);
        $response->assertViewIs('monhoc.index');
    }

    public function test_tim_kiem_mon_hoc(): void
    {
        $admin = $this->createAdmin();
        MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Lap trinh Python']);
        MonHoc::create(['ma_mon' => 'MH002', 'ten_mon' => 'Mang may tinh']);

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/monhoc?search=Python');

        $response->assertStatus(200);
    }

    // ===== CREATE & STORE =====

    public function test_hien_thi_form_them_mon_hoc(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/monhoc/create');

        $response->assertStatus(200);
        $response->assertViewIs('monhoc.create');
    }

    public function test_them_mon_hoc_thanh_cong(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/monhoc', [
            'ma_mon' => 'MH_NEW',
            'ten_mon' => 'Mon Hoc Moi',
        ]);

        $response->assertRedirect(route('monhoc.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('mon_hocs', ['ma_mon' => 'MH_NEW']);
    }

    public function test_them_mon_hoc_ma_trung(): void
    {
        $admin = $this->createAdmin();
        MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Existing']);

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/monhoc', [
            'ma_mon' => 'MH001',
            'ten_mon' => 'Another',
        ]);

        $response->assertSessionHasErrors('ma_mon');
    }

    // ===== EDIT & UPDATE =====

    public function test_hien_thi_form_chinh_sua_mon_hoc(): void
    {
        $admin = $this->createAdmin();
        $mh = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Mon Cu']);

        $response = $this->actingAs($admin, 'giangvien')->get("/admin/monhoc/{$mh->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('monhoc.edit');
    }

    public function test_cap_nhat_mon_hoc_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $mh = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Old Name']);

        $response = $this->actingAs($admin, 'giangvien')->put("/admin/monhoc/{$mh->id}", [
            'ma_mon' => 'MH001',
            'ten_mon' => 'New Name',
        ]);

        $response->assertRedirect(route('monhoc.index'));
        $this->assertDatabaseHas('mon_hocs', ['ten_mon' => 'New Name']);
    }

    // ===== DELETE =====

    public function test_xoa_mon_hoc_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $mh = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Delete Me']);

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/monhoc/{$mh->id}");

        $response->assertRedirect(route('monhoc.index'));
        $this->assertDatabaseMissing('mon_hocs', ['id' => $mh->id]);
    }

    public function test_xoa_mon_hoc_co_lich_thi_chua_dien_ra_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $mh = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Protected']);
        $year = now()->year;
        LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'chua_dien_ra',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/monhoc/{$mh->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('mon_hocs', ['id' => $mh->id]);
    }

    public function test_xoa_mon_hoc_co_lich_thi_dang_dien_ra_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $mh = MonHoc::create(['ma_mon' => 'MH002', 'ten_mon' => 'Dang Dien Ra']);
        $year = now()->year;
        LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P102',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'dang_dien_ra',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/monhoc/{$mh->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('mon_hocs', ['id' => $mh->id]);
    }
}
