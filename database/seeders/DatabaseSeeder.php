<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // CREAR ADMINISTRADOR PRINCIPAL
        User::create([
            'dni' => '00000000', // DNI ficticio para admin
            'name' => 'Administrador',
            'apellido_paterno' => 'Sistema',
            'apellido_materno' => 'GOREPA',
            'email' => 'admin@gorepa.gob.pe',
            'celular' => '999999999',
            'password' => Hash::make('admin123'), // Contraseña
            'status' => 1,      // 1 = Activo (Importante para que pueda entrar)
            'is_admin' => 1,    // 1 = Es Administrador
            
            // Campos de dirección (pueden ser nulos o genéricos)
            'departamento' => 'PASCO',
            'provincia' => 'PASCO',
            'distrito' => 'CHAUPIMARCA',
            'direccion' => 'Sede Central GORE',
        ]);

        // (Opcional) Un usuario de prueba normal
        /*
        User::create([
            'dni' => '88888888',
            'name' => 'Usuario',
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'Test',
            'email' => 'test@example.com',
            'celular' => '900000000',
            'password' => Hash::make('password'),
            'status' => 1,
            'is_admin' => 0,
        ]);
        */
    }
}