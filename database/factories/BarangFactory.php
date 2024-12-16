<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kd_brg' => $this->faker->unique()->bothify('#####'), // Kode barang dengan 5 karakter
            'nm_brg' => $this->faker->word(), // Nama barang
            'harga' => $this->faker->numberBetween(5000, 50000), // Harga dalam rentang 5000 - 50000
            'stok' => $this->faker->numberBetween(1, 100), // Stok dalam rentang 1 - 100
        ];
    }
}
