<?php

namespace Database\Factories;

use App\Models\LichThi;
use App\Models\MonHoc;
use Illuminate\Database\Eloquent\Factories\Factory;

class LichThiFactory extends Factory
{
    protected $model = LichThi::class;

    public function definition(): array
    {
        $year = now()->year;
        return [
            'mon_hoc_id' => MonHoc::factory(),
            'ngay_thi' => now()->addDays($this->faker->numberBetween(1, 30))->format('Y-m-d'),
            'gio_thi' => $this->faker->time('H:i:s', '17:00:00'),
            'thoi_luong_thi' => 60,
            'phong' => 'P' . $this->faker->numberBetween(101, 999),
            'ky_thi' => 'Giữa kỳ',
            'nam_hoc' => $year . '-' . ($year + 1),
            'trang_thai' => 'chua_dien_ra',
        ];
    }

    public function dangDienRa(): static
    {
        return $this->state(fn (array $attributes) => [
            'ngay_thi' => now()->format('Y-m-d'),
            'gio_thi' => now()->subMinutes(10)->format('H:i:s'),
            'thoi_luong_thi' => 120,
            'trang_thai' => 'dang_dien_ra',
        ]);
    }

    public function daKetThuc(): static
    {
        return $this->state(fn (array $attributes) => [
            'ngay_thi' => now()->subDays(1)->format('Y-m-d'),
            'gio_thi' => '08:00:00',
            'trang_thai' => 'da_ket_thuc',
        ]);
    }
}
