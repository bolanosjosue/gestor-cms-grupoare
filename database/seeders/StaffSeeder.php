<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        Staff::truncate();

        Staff::create([
            'name' => 'María López',
            'role' => 'Directora de Contenidos',
            'photo_url' => 'https://via.placeholder.com/300x300.png?text=Maria',
            'photo_alt' => 'Foto de María López',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Staff::create([
            'name' => 'Juan Pérez',
            'role' => 'Editor',
            'photo_url' => 'https://via.placeholder.com/300x300.png?text=Juan',
            'photo_alt' => 'Foto de Juan Pérez',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Staff::create([
            'name' => 'Ana García',
            'role' => 'Diseñadora',
            'photo_url' => 'https://via.placeholder.com/300x300.png?text=Ana',
            'photo_alt' => 'Foto de Ana García',
            'sort_order' => 3,
            'is_active' => true,
        ]);
    }
}

