<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use App\Models\PhanCongGV;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhanCongTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): GiangVien
    {
        return GiangVien::factory()->admin()->create();
    }

    private function createGV(array $attributes = []): GiangVien
    {
        return GiangVien::factory()->create(array_merge(['vai_tro' => 'giang_vien'], $attributes));
    }

    private function createLichThi(array $attributes = []): LichThi
    {
        $mh = MonHoc::factory()->create();
        return LichThi::factory()->create(array_merge([
            'mon_hoc_id' => $mh->id,
            'thoi_luong_thi' => 60,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'trang_thai' => 'chua_dien_ra',
        ], $attributes));
    }

    // ===== LICH THI PHAN CONG =====

    public function test_admin_xem_form_phan_cong_lich_thi(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $gv = $this->createGV();

        $response = $this->actingAs($admin, 'giangvien')->get(route('lichthi.phancong', $lt->id));

        $response->assertStatus(200);
        $response->assertViewIs('lichthi.phancong');
        $response->assertViewHas('giangViens', function ($giangViens) use ($gv) {
            return $giangViens->contains('id', $gv->id);
        });
    }

    public function test_phan_cong_giang_vien_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $gv = $this->createGV();

        $response = $this->actingAs($admin, 'giangvien')->post(route('lichthi.phancong.save', $lt->id), [
            'lich_thi_id' => $lt->id,
            'giang_vien_id' => $gv->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('phan_cong_gvs', [
            'lich_thi_id' => $lt->id,
            'giang_vien_id' => $gv->id,
        ]);
    }

    public function test_phan_cong_bi_chan_khi_tai_khoan_giang_vien_bi_khoa(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi();
        $gv = $this->createGV(['is_active' => false]);

        $response = $this->actingAs($admin, 'giangvien')->post(route('lichthi.phancong.save', $lt->id), [
            'lich_thi_id' => $lt->id,
            'giang_vien_id' => $gv->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('phan_cong_gvs', [
            'lich_thi_id' => $lt->id,
            'giang_vien_id' => $gv->id,
        ]);
    }

    public function test_phan_cong_bi_chan_khi_giang_vien_bi_trung_gio(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV();

        $ngay = now()->addDays(5)->format('Y-m-d');
        // Lịch 1: 08:00 - 09:00
        $lt1 = $this->createLichThi(['ngay_thi' => $ngay, 'gio_thi' => '08:00:00', 'thoi_luong_thi' => 60]);
        PhanCongGV::create(['lich_thi_id' => $lt1->id, 'giang_vien_id' => $gv->id]);

        // Lịch 2: 08:30 - 09:30 (trùng với ca 1)
        $lt2 = $this->createLichThi(['ngay_thi' => $ngay, 'gio_thi' => '08:30:00', 'thoi_luong_thi' => 60]);

        $response = $this->actingAs($admin, 'giangvien')->post(route('lichthi.phancong.save', $lt2->id), [
            'lich_thi_id' => $lt2->id,
            'giang_vien_id' => $gv->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('phan_cong_gvs', [
            'lich_thi_id' => $lt2->id,
            'giang_vien_id' => $gv->id,
        ]);
    }

    public function test_xoa_phan_cong_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi(['trang_thai' => 'chua_dien_ra']);
        $gv = $this->createGV();
        $pc = PhanCongGV::create(['lich_thi_id' => $lt->id, 'giang_vien_id' => $gv->id]);

        $response = $this->actingAs($admin, 'giangvien')->delete(route('lichthi.phancong.xoa', [
            'lichthi' => $lt->id,
            'phancong' => $pc->id,
        ]));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('phan_cong_gvs', ['id' => $pc->id]);
    }

    public function test_xoa_phan_cong_bi_chan_khi_ca_thi_dang_dien_ra(): void
    {
        $admin = $this->createAdmin();
        $lt = $this->createLichThi(['trang_thai' => 'dang_dien_ra']);
        $gv = $this->createGV();
        $pc = PhanCongGV::create(['lich_thi_id' => $lt->id, 'giang_vien_id' => $gv->id]);

        $response = $this->actingAs($admin, 'giangvien')->delete(route('lichthi.phancong.xoa', [
            'lichthi' => $lt->id,
            'phancong' => $pc->id,
        ]));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('phan_cong_gvs', ['id' => $pc->id]);
    }

    // ===== GIANG VIEN ASSIGN / UNASSIGN (AJAX) =====

    public function test_assign_giang_vien_ajax_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV();
        $lt = $this->createLichThi();

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('giangvien.assign', [
            'giangvien' => $gv->id,
            'lichthi' => $lt->id,
        ]));

        $response->assertJson([
            'status' => 'success',
            'message' => 'Phân công thành công!',
        ]);
        $this->assertDatabaseHas('phan_cong_gvs', [
            'lich_thi_id' => $lt->id,
            'giang_vien_id' => $gv->id,
        ]);
    }

    public function test_assign_giang_vien_ajax_chan_khi_tai_khoan_khoa(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV(['is_active' => false]);
        $lt = $this->createLichThi();

        $response = $this->actingAs($admin, 'giangvien')->postJson(route('giangvien.assign', [
            'giangvien' => $gv->id,
            'lichthi' => $lt->id,
        ]));

        $response->assertJson([
            'status' => 'error',
        ]);
    }

    public function test_unassign_giang_vien_ajax_thanh_cong(): void
    {
        $admin = $this->createAdmin();
        $gv = $this->createGV();
        $lt = $this->createLichThi(['trang_thai' => 'chua_dien_ra']);
        $gv->lichthis()->attach($lt->id);

        $response = $this->actingAs($admin, 'giangvien')->deleteJson(route('giangvien.unassign', [
            'giangvien' => $gv->id,
            'lichthi' => $lt->id,
        ]));

        $response->assertJson([
            'status' => 'success',
            'message' => 'Đã hủy phân công thành công!',
        ]);
        $this->assertDatabaseMissing('phan_cong_gvs', [
            'lich_thi_id' => $lt->id,
            'giang_vien_id' => $gv->id,
        ]);
    }
}
