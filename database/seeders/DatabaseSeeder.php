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
        // User::factory(10)->create();
        \App\Models\User::create([
        'name' => 'Admin GOREPA',
        'email' => 'admin@gorepa.gob.pe',
        'dni' => '00000000',
        'password' => bcrypt('admin123'), // Usa una contraseña segura
        'is_admin' => true,
    ]);
    
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
