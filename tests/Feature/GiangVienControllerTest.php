<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use App\Models\PhanCongGV;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class GiangVienControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(array $overrides = []): GiangVien
    {
        return GiangVien::create(array_merge([
            'ma_gv' => 'ADMIN01',
            'ho_ten' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'vai_tro' => 'admin',
            'is_active' => true,
        ], $overrides));
    }

    private function createGV(array $overrides = []): GiangVien
    {
        static $counter = 0;
        $counter++;
        return GiangVien::create(array_merge([
            'ma_gv' => 'GV' . str_pad($counter, 4, '0', STR_PAD_LEFT),
            'ho_ten' => 'Giang Vien ' . $counter,
            'email' => "gv{$counter}@test.com",
            'password' => Hash::make('password'),
            'vai_tro' => 'giang_vien',
            'is_active' => true,
        ], $overrides));
    }

    // ===== INDEX =====

    public function test_admin_xem_danh_sach_giang_vien(): void
    {
        $admin = $this->createAdmin();
        $this->createGV();

        $response = $this->actingAs($admin, 'giangvien')->get('/admin/giangvien');

        $response->assertStatus(200);
        $response->assertViewIs('giangvien.index');
    }

    // ===== STORE =====

    public function test_them_giang_vien_thanh_cong(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/giangvien', [
            'ma_gv' => 'GV_NEW',
            'ho_ten' => 'New GV',
            'email' => 'newgv@test.com',
            'password' => 'password123',
            'vai_tro' => 'giang_vien',
        ]);

        $response->assertRedirect(route('giangvien.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('giang_viens', ['ma_gv' => 'GV_NEW']);
    }

    public function test_them_giang_vien_ma_trung(): void
    {
        $admin = $this->createAdmin();
        $this->createGV(['ma_gv' => 'GV_DUP']);

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/giangvien', [
            'ma_gv' => 'GV_DUP',
            'ho_ten' => 'Test',
            'email' => 'unique@test.com',
            'password' => 'password123',
            'vai_tro' => 'giang_vien',
        ]);

        $response->assertSessionHasErrors('ma_gv');
    }

    public function test_them_giang_vien_mat_khau_qua_ngan(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->post('/admin/giangvien', [
            'ma_gv' => 'GV_NEW',
            'ho_ten' => 'Test',
            'email' => 'test@test.com',
            'password' => '123',
            'vai_tro' => 'giang_vien',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ===== UPDATE =====

    public function test_cap_nhat_giang_vien_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV();

        $response = $this->actingAs($admin, 'giangvien')->put("/admin/giangvien/{$gv->id}", [
            'ma_gv' => $gv->ma_gv,
            'ho_ten' => 'Updated Name',
            'email' => $gv->email,
        ]);

        $response->assertRedirect(route('giangvien.index'));
        $this->assertDatabaseHas('giang_viens', ['id' => $gv->id, 'ho_ten' => 'Updated Name']);
    }

    // ===== DELETE =====

    public function test_xoa_giang_vien_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV();

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/giangvien/{$gv->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('giang_viens', ['id' => $gv->id]);
    }

    public function test_xoa_giang_vien_co_phan_cong_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV();
        $monHoc = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Test Mon']);
        $year = now()->year;
        $lichThi = LichThi::create([
            'mon_hoc_id' => $monHoc->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);
        PhanCongGV::create(['lich_thi_id' => $lichThi->id, 'giang_vien_id' => $gv->id]);

        $response = $this->actingAs($admin, 'giangvien')->delete("/admin/giangvien/{$gv->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('giang_viens', ['id' => $gv->id]);
    }

    // ===== TOGGLE =====

    public function test_toggle_khoa_giang_vien(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV(['is_active' => true]);

        $response = $this->actingAs($admin, 'giangvien')->patch("/admin/giangvien/{$gv->id}/toggle");

        $response->assertSessionHas('success');
        $gv->refresh();
        $this->assertFalse((bool) $gv->is_active);
    }

    public function test_toggle_mo_khoa_giang_vien(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV(['is_active' => false]);

        $response = $this->actingAs($admin, 'giangvien')->patch("/admin/giangvien/{$gv->id}/toggle");

        $response->assertSessionHas('success');
        $gv->refresh();
        $this->assertTrue((bool) $gv->is_active);
    }

    public function test_toggle_khong_the_khoa_chinh_minh(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->patch("/admin/giangvien/{$admin->id}/toggle");

        $response->assertSessionHas('error');
        $admin->refresh();
        $this->assertTrue((bool) $admin->is_active);
    }

    public function test_toggle_khoa_giang_vien_co_lich_thi_sap_toi_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV(['is_active' => true]);
        $monHoc = MonHoc::create(['ma_mon' => 'MH002', 'ten_mon' => 'Mon Thi Test']);
        $year = now()->year;
        $lichThi = LichThi::create([
            'mon_hoc_id' => $monHoc->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);
        PhanCongGV::create(['lich_thi_id' => $lichThi->id, 'giang_vien_id' => $gv->id]);

        $response = $this->actingAs($admin, 'giangvien')->patch("/admin/giangvien/{$gv->id}/toggle");

        $response->assertSessionHas('error');
        $gv->refresh();
        $this->assertTrue((bool) $gv->is_active);
    }
}
