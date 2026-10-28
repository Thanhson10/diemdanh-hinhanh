<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use App\Models\PhanCongGV;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_khach_truy_cap_trang_chu_thanh_cong(): void
    {
        $response = $this->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home.index');
        $response->assertViewHas('lichThis', function ($lichThis) {
            return $lichThis->isEmpty();
        });
    }

    public function test_giang_vien_chi_thay_lich_thi_dang_dien_ra_duoc_phan_cong(): void
    {
        $gv = GiangVien::factory()->create();
        $gvKhac = GiangVien::factory()->create();
        $mh = MonHoc::factory()->create(['ten_mon' => 'Lập trình PHP']);

        // Lịch thi 1: đang diễn ra, được phân công cho $gv
        $lt1 = LichThi::factory()->dangDienRa()->create([
            'mon_hoc_id' => $mh->id,
            'phong' => 'P101',
        ]);
        PhanCongGV::create(['lich_thi_id' => $lt1->id, 'giang_vien_id' => $gv->id]);

        // Lịch thi 2: đang diễn ra, nhưng phân công cho $gvKhac
        $lt2 = LichThi::factory()->dangDienRa()->create([
            'mon_hoc_id' => $mh->id,
            'phong' => 'P102',
        ]);
        PhanCongGV::create(['lich_thi_id' => $lt2->id, 'giang_vien_id' => $gvKhac->id]);

        // Lịch thi 3: chưa diễn ra, phân công cho $gv
        $lt3 = LichThi::factory()->create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->addDays(5)->format('Y-m-d'),
            'trang_thai' => 'chua_dien_ra',
        ]);
        PhanCongGV::create(['lich_thi_id' => $lt3->id, 'giang_vien_id' => $gv->id]);

        $response = $this->actingAs($gv, 'giangvien')->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertViewHas('lichThis', function ($lichThis) use ($lt1, $lt2, $lt3) {
            $ids = $lichThis->pluck('id')->all();
            return in_array($lt1->id, $ids) && !in_array($lt2->id, $ids) && !in_array($lt3->id, $ids);
        });
    }

    public function test_trang_chu_tu_dong_cap_nhat_trang_thai_lich_thi_het_gio(): void
    {
        $mh = MonHoc::factory()->create();
        $lt = LichThi::create([
            'mon_hoc_id' => $mh->id,
            'ngay_thi' => now()->subDays(2)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'thoi_luong_thi' => 60,
            'phong' => 'P101',
            'ky_thi' => 'GK',
            'nam_hoc' => now()->year . '-' . (now()->year + 1),
            'trang_thai' => 'chua_dien_ra',
        ]);

        $this->assertEquals('chua_dien_ra', $lt->trang_thai);

        $this->get(route('home.index'));

        $lt->refresh();
        $this->assertEquals('da_ket_thuc', $lt->trang_thai);
    }

    public function test_tim_kiem_lich_thi_theo_phong_va_ten_mon(): void
    {
        $gv = GiangVien::factory()->create();
        $mhPHP = MonHoc::factory()->create(['ten_mon' => 'Lập trình PHP']);
        $mhJava = MonHoc::factory()->create(['ten_mon' => 'Lập trình Java']);

        $lt1 = LichThi::factory()->dangDienRa()->create([
            'mon_hoc_id' => $mhPHP->id,
            'phong' => 'A101',
        ]);
        PhanCongGV::create(['lich_thi_id' => $lt1->id, 'giang_vien_id' => $gv->id]);

        $lt2 = LichThi::factory()->dangDienRa()->create([
            'mon_hoc_id' => $mhJava->id,
            'phong' => 'B202',
        ]);
        PhanCongGV::create(['lich_thi_id' => $lt2->id, 'giang_vien_id' => $gv->id]);

        // Tìm theo phòng A101
        $response = $this->actingAs($gv, 'giangvien')->get(route('home.index', ['search' => 'A101']));
        $response->assertStatus(200);
        $response->assertViewHas('lichThis', function ($lichThis) use ($lt1, $lt2) {
            $ids = $lichThis->pluck('id')->all();
            return in_array($lt1->id, $ids) && !in_array($lt2->id, $ids);
        });

        // Tìm theo tên môn Java
        $response2 = $this->actingAs($gv, 'giangvien')->get(route('home.index', ['search' => 'Java']));
        $response2->assertStatus(200);
        $response2->assertViewHas('lichThis', function ($lichThis) use ($lt1, $lt2) {
            $ids = $lichThis->pluck('id')->all();
            return in_array($lt2->id, $ids) && !in_array($lt1->id, $ids);
        });
    }
}
