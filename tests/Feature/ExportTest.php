<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use App\Models\MonHoc;
use App\Models\LichThi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): GiangVien
    {
        return GiangVien::factory()->admin()->create();
    }

    public function test_download_mau_sinh_vien_excel(): void
    {
        Excel::fake();
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'giangvien')->get(route('download.mau.sinhvien'));

        $response->assertStatus(200);
        Excel::assertDownloaded('mau_sinh_vien.xlsx');
    }

    public function test_export_danh_sach_diem_danh_lich_thi_excel(): void
    {
        Excel::fake();
        $admin = $this->createAdmin();
        $mh = MonHoc::factory()->create(['ten_mon' => 'Cong nghe Web']);
        $lt = LichThi::factory()->create([
            'mon_hoc_id' => $mh->id,
            'phong' => 'P101',
        ]);

        $response = $this->actingAs($admin, 'giangvien')->get(route('lichthi.export', $lt->id));

        $response->assertStatus(200);
        $expectedName = \Illuminate\Support\Str::slug($mh->ten_mon) . '_' . \Illuminate\Support\Str::slug($lt->phong) . '_'
            . $lt->thoi_gian_thi->format('d-m-Y') . '_' . $lt->thoi_gian_thi->format('G') . 'h' . $lt->thoi_gian_thi->format('i') . '_'
            . \Illuminate\Support\Str::slug($lt->ky_thi) . '_' . \Illuminate\Support\Str::slug($lt->nam_hoc) . '.xlsx';
        Excel::assertDownloaded($expectedName);
    }
}
