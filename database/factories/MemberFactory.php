<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->firstName().' '.fake()->lastName();

        return [
            'nama' => $name,
            'nim' => fake()->unique()->numerify('##########'),
            'email' => Str::slug($name, '.').fake()->unique()->numerify('###').'@pens.ac.id',
            'nomor_telepon' => fake()->numerify('08##########'),
            'alamat' => fake()->address(),
            'status' => fake()->randomElement(['aktif', 'aktif', 'aktif', 'nonaktif']),
        ];
    }
}
