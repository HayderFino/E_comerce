<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear el Rol de Super Admin si no existe
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // 2. Crear usuario para Factus con permisos
        $factusUser = User::firstOrCreate(
            ['email' => 'factus@mitienda.com'],
            [
                'name' => 'Revisor Factus',
                'password' => bcrypt('Factus2026*'), // Contraseña segura
                'email_verified_at' => now(),
            ]
        );
        
        // Asignar rol
        if (!$factusUser->hasRole('Super Admin')) {
            $factusUser->assignRole($role);
        }

        // Crear otro usuario normal por si acaso
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // 3. Crear 10 productos de prueba
        $productos = [
            ['Teclado Mecánico RGB', 'Teclado mecánico con switches azules', 150000],
            ['Ratón Inalámbrico', 'Ratón ergonómico inalámbrico 2.4GHz', 85000],
            ['Monitor 24" IPS', 'Monitor Full HD 1080p', 550000],
            ['Audífonos Bluetooth', 'Audífonos con cancelación de ruido', 200000],
            ['Silla Gamer Ergonómica', 'Silla ajustable con soporte lumbar', 600000],
            ['Escritorio Ajustable', 'Escritorio con ajuste de altura eléctrico', 1200000],
            ['Cámara Web 1080p', 'Cámara con micrófono integrado', 120000],
            ['Micrófono Condensador', 'Micrófono USB para podcasting', 180000],
            ['Disco Duro Externo 1TB', 'Almacenamiento portátil USB 3.0', 250000],
            ['Memoria RAM 16GB', 'Módulo de memoria DDR4 3200MHz', 190000],
        ];

        foreach ($productos as $index => $prod) {
            Product::firstOrCreate(
                ['name' => $prod[0]],
                [
                    'slug' => Str::slug($prod[0]),
                    'description' => $prod[1],
                    'price' => $prod[2],
                    'stock' => rand(10, 50),
                    'is_active' => true,
                    'tax_code' => '01', // IVA
                    'tax_rate' => 19.00,
                    'is_tax_excluded' => false,
                ]
            );
        }
    }
}
