<?php

namespace Database\Factories;

use App\Models\DiemDanh;
use App\Models\LichThi;
use App\Models\SinhVien;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiemDanhFactory extends Factory
{
    protected $model = DiemDanh::class;

    public function definition(): array
    {
        return [
            'lich_thi_id' => LichThi::factory(),
            'sinh_vien_id' => SinhVien::factory(),
            'ket_qua' => null,
            'do_chinh_xac' => null,
            'thoi_gian_dd' => null,
            'hinh_thuc_dd' => null,
            'ghi_chu' => null,
        ];
    }

    public function hopLe(): static
    {
        return $this->state(fn (array $attributes) => [
            'ket_qua' => 'hợp lệ',
            'do_chinh_xac' => '95.5',
            'thoi_gian_dd' => now(),
            'hinh_thuc_dd' => 'Camera',
        ]);
    }

    public function thuCong(): static
    {
        return $this->state(fn (array $attributes) => [
            'ket_qua' => 'hợp lệ',
            'do_chinh_xac' => '100',
            'thoi_gian_dd' => now(),
            'hinh_thuc_dd' => 'Thủ công',
        ]);
    }
}
