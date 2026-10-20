<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Tim IT Kesbangpol
        User::updateOrCreate(
            ['email' => 'timitkesbangpol@gmail.com'],
            [
                'name' => 'Tim IT Kesbangpol',
                'password' => \Illuminate\Support\Facades\Hash::make('B4kesbangpol#IT2026!'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Akun Sekretariat Kesbangpol
        User::updateOrCreate(
            ['email' => 'kesbangplsekre@gmail.com'],
            [
                'name' => 'Sekretariat Kesbangpol',
                'password' => \Illuminate\Support\Facades\Hash::make('B4kesbangpol@Sekre2026!'),
                'email_verified_at' => now(),
            ]
        );

        // Hapus akun selain 2 akun resmi di atas
        User::whereNotIn('email', ['timitkesbangpol@gmail.com', 'kesbangplsekre@gmail.com'])->delete();
    }
}
