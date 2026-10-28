<?php

namespace Database\Factories;

use App\Models\MonHoc;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonHocFactory extends Factory
{
    protected $model = MonHoc::class;

    public function definition(): array
    {
        return [
            'ma_mon' => strtoupper($this->faker->unique()->bothify('MH###')),
            'ten_mon' => $this->faker->words(3, true),
        ];
    }
}
