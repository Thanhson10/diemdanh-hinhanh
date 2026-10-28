<?php

namespace Database\Factories;

use App\Models\SinhVien;
use Illuminate\Database\Eloquent\Factories\Factory;

class SinhVienFactory extends Factory
{
    protected $model = SinhVien::class;

    public function definition(): array
    {
        return [
            'ma_sv' => strtoupper($this->faker->unique()->bothify('??########')),
            'ho_ten' => $this->faker->name(),
            'lop' => 'CNTT' . $this->faker->numberBetween(1, 4),
            'email' => $this->faker->unique()->safeEmail(),
            'do_chinh_xac_tb' => null,
            'so_lan_nhan_dien' => 0,
        ];
    }
}
