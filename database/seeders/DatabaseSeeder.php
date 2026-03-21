<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'nicola.fabbozzo@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('nicola#VZ!9284'),
            ]
        );

        $this->call([
            StaffSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
