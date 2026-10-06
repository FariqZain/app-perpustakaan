<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@pens.ac.id',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Petugas Satu',
            'email' => 'petugas1@pens.ac.id',
            'password' => 'password',
            'role' => 'petugas',
        ]);
    }
}
