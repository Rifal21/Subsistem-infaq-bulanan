<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'Rifal Kurniawan',
            'email' => 'rifal@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('123123123'), // password
        ]);

        Kelas::create([
            'kode' => 'A.1',
            'nama' => 'Kelas A1',
        ]);
        Kelas::create([
            'kode' => 'A.2',
            'nama' => 'Kelas A2',
        ]);
        Kelas::create([
            'kode' => 'B.3',
            'nama' => 'Kelas B3',
        ]);
        Kelas::create([
            'kode' => 'A.3',
            'nama' => 'Kelas A3',
        ]);
    }
}
