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

class LichThiControllerTest extends TestCase
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

    private function createMonHoc(): MonHoc
    {
        return MonHoc::create(['ma_mon' => 'MH001', 'ten_mon' => 'Test Mon']);
    }

    private function validLichThiData(int $monHocId): array
    {
        $year = now()->year;
        return [
            'mon_hoc_id' => $monHocId,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'Giữa kỳ',
            'nam_hoc' => $year . '-' . ($year + 1),
        ];
    }

    // ===== INDEX =====

    public function test_xem_danh_sach_lich_thi(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->get(route('lichthi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('lichthi.index');
    }

    public function test_guest_khong_xem_duoc_lich_thi(): void
    {
        $response = $this->get(route('lichthi.index'));
        $response->assertRedirect(route('login'));
    }

    // ===== STORE =====

    public function test_admin_tao_lich_thi_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();

        $response = $this->actingAs($admin, 'giangvien')->post(route('lichthi.store'), $this->validLichThiData($mh->id));

        $response->assertRedirect(route('lichthi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lich_this', ['mon_hoc_id' => $mh->id, 'phong' => 'P101']);
    }

    public function test_tao_lich_thi_thoi_gian_qua_khu_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();

        $data = $this->validLichThiData($mh->id);
        $data['ngay_thi'] = now()->subDays(1)->format('Y-m-d');
        $data['gio_thi'] = '08:00';

        $response = $this->actingAs($admin, 'giangvien')->post(route('lichthi.store'), $data);

        $response->assertSessionHasErrors(['ngay_thi']);
    }

    public function test_tao_lich_thi_trung_phong_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;

        // Create existing exam in P101
        LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);

        // Try to create another exam in same room, overlapping time
        $data = $this->validLichThiData($mh->id);
        $data['gio_thi'] = '09:30'; // overlaps with 09:00-10:00

        $response = $this->actingAs($admin, 'giangvien')->post(route('lichthi.store'), $data);

        $response->assertSessionHasErrors(['phong']);
    }

    public function test_tao_lich_thi_validation_truong_bat_buoc(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->post(route('lichthi.store'), []);

        $response->assertSessionHasErrors(['mon_hoc_id', 'ngay_thi', 'gio_thi', 'phong', 'ky_thi', 'nam_hoc', 'thoi_luong_thi']);
    }

    // ===== UPDATE =====

    public function test_cap_nhat_lich_thi_chua_dien_ra(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'chua_dien_ra',
        ]);

        $data = $this->validLichThiData($mh->id);
        $data['phong'] = 'P202';

        $response = $this->actingAs($admin, 'giangvien')->put(route('lichthi.update', $lt->id), $data);

        $response->assertRedirect(route('lichthi.index'));
        $this->assertDatabaseHas('lich_this', ['id' => $lt->id, 'phong' => 'P202']);
    }

    public function test_cap_nhat_lich_thi_da_ket_thuc_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->subDays(1)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'da_ket_thuc',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->put(route('lichthi.update', $lt->id), $this->validLichThiData($mh->id));

        $response->assertRedirect(route('lichthi.index'));
        $response->assertSessionHas('error');
    }

    // ===== DELETE =====

    public function test_xoa_lich_thi_chua_dien_ra(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'chua_dien_ra',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->delete(route('lichthi.destroy', $lt->id));

        $response->assertRedirect(route('lichthi.index'));
        $this->assertDatabaseMissing('lich_this', ['id' => $lt->id]);
    }

    public function test_xoa_lich_thi_da_ket_thuc_bi_chan(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->subDays(1)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'da_ket_thuc',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->delete(route('lichthi.destroy', $lt->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('lich_this', ['id' => $lt->id]);
    }

    // ===== SHOW =====

    public function test_xem_chi_tiet_lich_thi(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);

        $response = $this->actingAs($admin, 'giangvien')->get(route('lichthi.show', $lt->id));

        $response->assertStatus(200);
        $response->assertViewIs('lichthi.show');
    }

    // ===== ADD STUDENTS =====

    public function test_them_sinh_vien_vao_ca_thi(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);

        $sv1 = SinhVien::factory()->create(['ma_sv' => 'SV00000001']);
        $sv2 = SinhVien::factory()->create(['ma_sv' => 'SV00000002']);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('lichthi.addStudents', $lt->id), [
            'mssv' => ['SV00000001', 'SV00000002'],
        ]);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('diem_danhs', ['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv1->id]);
        $this->assertDatabaseHas('diem_danhs', ['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv2->id]);
    }

    public function test_them_sinh_vien_da_co_bi_skip(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);
        $sv = SinhVien::factory()->create(['ma_sv' => 'SV00000001']);
        DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('lichthi.addStudents', $lt->id), [
            'mssv' => ['SV00000001'],
        ]);

        $response->assertJson(['success' => false]);
        $this->assertEquals(1, DiemDanh::where('lich_thi_id', $lt->id)->count());
    }

    // ===== REMOVE STUDENT =====

    public function test_xoa_sinh_vien_khoi_ca_thi(): void
    {
        $admin = $this->createAdmin();
        $mh = $this->createMonHoc();
        $year = now()->year;
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(10)->format('Y-m-d'),
            'gio_thi' => '09:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => $year . '-' . ($year + 1),
        ]);
        $sv = SinhVien::factory()->create();
        $dd = DiemDanh::create(['lich_thi_id' => $lt->id, 'sinh_vien_id' => $sv->id]);

        $response = $this->actingAs($admin, 'giangvien')->delete(route('lichthi.removeStudent', $dd->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('diem_danhs', ['id' => $dd->id]);
    }
}
