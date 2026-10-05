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

        // 3. Crear 10 productos de prueba con imágenes
        $productos = [
            ['Teclado Mecánico RGB', 'Teclado mecánico con switches azules', 150000, 'https://images.unsplash.com/photo-1595225476474-87563907a212?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Ratón Inalámbrico', 'Ratón ergonómico inalámbrico 2.4GHz', 85000, 'https://images.unsplash.com/photo-1527814050087-179f00222fb8?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Monitor 24" IPS', 'Monitor Full HD 1080p', 550000, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Audífonos Bluetooth', 'Audífonos con cancelación de ruido', 200000, 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Silla Gamer Ergonómica', 'Silla ajustable con soporte lumbar', 600000, 'https://images.unsplash.com/photo-1598550476439-6847785fcea6?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Escritorio Ajustable', 'Escritorio con ajuste de altura eléctrico', 1200000, 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Cámara Web 1080p', 'Cámara con micrófono integrado', 120000, 'https://images.unsplash.com/photo-1587826644265-4f36c4b26710?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Micrófono Condensador', 'Micrófono USB para podcasting', 180000, 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Disco Duro Externo 1TB', 'Almacenamiento portátil USB 3.0', 250000, 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
            ['Memoria RAM 16GB', 'Módulo de memoria DDR4 3200MHz', 190000, 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3'],
        ];

        foreach ($productos as $index => $prod) {
            Product::updateOrCreate(
                ['name' => $prod[0]],
                [
                    'slug' => Str::slug($prod[0]),
                    'description' => $prod[1],
                    'price' => $prod[2],
                    'stock' => rand(10, 50),
                    'image_url' => $prod[3],
                    'is_active' => true,
                    'tax_code' => '01', // IVA
                    'tax_rate' => 19.00,
                    'is_tax_excluded' => false,
                ]
            );
        }
    }
}
