<?php

namespace Database\Factories;

use App\Models\AnhTrainSv;
use App\Models\SinhVien;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnhTrainSvFactory extends Factory
{
    protected $model = AnhTrainSv::class;

    public function definition(): array
    {
        return [
            'sinh_vien_id' => SinhVien::factory(),
            'hinh_anh' => 'faces/' . $this->faker->uuid() . '.jpg',
            'face_id' => json_encode([$this->faker->uuid()]),
            'file_hash' => md5($this->faker->unique()->text()),
            'file_name' => $this->faker->word() . '.jpg',
            'trang_thai' => 'trained',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['trang_thai' => 'pending', 'hinh_anh' => null, 'face_id' => null]);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['trang_thai' => 'failed', 'hinh_anh' => null, 'face_id' => null]);
    }
}
