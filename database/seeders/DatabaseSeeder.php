<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Ruangan;
use App\Models\Karyawan;

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

        Schema::disableForeignKeyConstraints();

        // Kosongkan tabel (truncate) di sini
        // Pastikan urutannya benar, tabel yang dirujuk (ruangans)
        // dikosongkan sebelum tabel yang merujuk (karyawans)
        Ruangan::truncate();
        // Karyawan::truncate(); // jika ada seeder karyawan

        // Aktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            RuanganSeeder::class,
        ]);
    }
}
