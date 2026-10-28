<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use App\Models\SinhVien;
use App\Models\DiemDanh;
use App\Models\PhanCongGV;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class DiemDanhControllerTest extends TestCase
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

    private function createLichThi(): LichThi
    {
        $mh = MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Test']);
        $year = now()->year;
        return LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);
    }

    // ===== INDEX =====

    public function test_xem_danh_sach_diem_danh(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->get(route('diemdanh.index'));

        $response->assertStatus(200);
        $response->assertViewIs('diemdanh.index');
    }

    public function test_guest_khong_xem_duoc_diem_danh(): void
    {
        $response = $this->get(route('diemdanh.index'));
        $response->assertRedirect(route('login'));
    }

    // ===== SHOW =====

    public function test_xem_chi_tiet_diem_danh(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        PhanCongGV::create(['lich_thi_id' => $lt->id, 'giang_vien_id' => $admin->id]);

        $response = $this->actingAs($admin, 'giangvien')->get(route('diemdanh.show', $lt->id));

        $response->assertStatus(200);
        $response->assertViewIs('diemdanh.show');
    }

    // ===== TOGGLE =====

    public function test_toggle_diem_danh_thu_cong(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create();
        $dd = DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('diemdanh.toggle'), [
            'id' => $dd->id,
            'checked' => true,
        ]);

        $response->assertJson(['success' => true]);
        $dd->refresh();
        $this->assertEquals('hợp lệ', $dd->ket_qua);
        $this->assertEquals('Thủ công', $dd->hinh_thuc_dd);
        $this->assertEquals(100, $dd->do_chinh_xac);
    }

    public function test_toggle_huy_diem_danh(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create();
        $dd = DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv->id,
            'ket_qua' => 'hợp lệ',
            'hinh_thuc_dd' => 'Thủ công',
            'do_chinh_xac' => '100',
            'thoi_gian_dd' => now(),
        ]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('diemdanh.toggle'), [
            'id' => $dd->id,
            'checked' => false,
        ]);

        $response->assertJson(['success' => true]);
        $dd->refresh();
        $this->assertNull($dd->ket_qua);
        $this->assertNull($dd->hinh_thuc_dd);
    }

    // ===== GHI CHU =====

    public function test_cap_nhat_ghi_chu_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create();
        $dd = DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv->id,
            'ket_qua' => 'hợp lệ',
            'hinh_thuc_dd' => 'Thủ công',
            'do_chinh_xac' => '100',
            'thoi_gian_dd' => now(),
        ]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('diemdanh.ghichu', $dd->id), [
            'ghi_chu' => 'chua_co_anh',
        ]);

        $response->assertJson(['success' => true]);
        $dd->refresh();
        $this->assertEquals('chua_co_anh', $dd->ghi_chu);
    }

    public function test_cap_nhat_ghi_chu_khong_hop_le(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create();
        $dd = DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv->id,
            'ket_qua' => 'hợp lệ',
            'hinh_thuc_dd' => 'Camera',  // NOT 'Thủ công'
            'do_chinh_xac' => '95',
            'thoi_gian_dd' => now(),
        ]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('diemdanh.ghichu', $dd->id), [
            'ghi_chu' => 'chua_co_anh',
        ]);

        $response->assertStatus(400);
    }

    public function test_xoa_ghi_chu(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $sv = SinhVien::factory()->create();
        $dd = DiemDanh::create([
            'lich_thi_id' => $lt->id,
            'sinh_vien_id' => $sv->id,
            'ghi_chu' => 'chua_co_anh',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('diemdanh.ghichu', $dd->id), [
            'ghi_chu' => null,
        ]);

        $response->assertJson(['success' => true]);
        $dd->refresh();
        $this->assertNull($dd->ghi_chu);
    }
}
