<?php

namespace Database\Factories;

use App\Models\GiangVien;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class GiangVienFactory extends Factory
{
    protected $model = GiangVien::class;

    public function definition(): array
    {
        return [
            'ma_gv' => strtoupper($this->faker->unique()->bothify('GV####')),
            'ho_ten' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'vai_tro' => 'giang_vien',
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'vai_tro' => 'admin',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
