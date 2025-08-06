<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Property::create([
            'title' => 'Casa familiar en las afueras',
            'description' => 'Hermosa casa de 3 habitaciones con jardín privado, perfecta para familias. Ubicada en una zona tranquila con fácil acceso al transporte público.',
            'price' => 350000.00,
            'type' => 'sale',
            'category' => 'house',
            'address' => 'Calle Principal 123',
            'city' => 'Madrid',
            'state' => 'Madrid',
            'zip_code' => '28001',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 120.50,
            'images' => ['house1.jpg', 'house1_2.jpg'],
            'features' => ['Jardín', 'Garaje', 'Terraza', 'Calefacción'],
            'agent_id' => 1,
            'status' => 'available',
        ]);

        \App\Models\Property::create([
            'title' => 'Apartamento moderno en el centro',
            'description' => 'Apartamento completamente renovado en el corazón de la ciudad. Cuenta con todas las comodidades modernas y excelente conexión de transporte.',
            'price' => 1200.00,
            'type' => 'rent',
            'category' => 'apartment',
            'address' => 'Gran Vía 456',
            'city' => 'Madrid',
            'state' => 'Madrid',
            'zip_code' => '28013',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'area' => 80.00,
            'images' => ['apt1.jpg'],
            'features' => ['Ascensor', 'Aire acondicionado', 'Internet fibra'],
            'agent_id' => 2,
            'status' => 'available',
        ]);

        \App\Models\Property::create([
            'title' => 'Oficina en zona comercial',
            'description' => 'Espaciosa oficina en el distrito financiero, ideal para empresas en crecimiento. Incluye estacionamiento y seguridad 24/7.',
            'price' => 2500.00,
            'type' => 'rent',
            'category' => 'office',
            'address' => 'Paseo de la Castellana 789',
            'city' => 'Madrid',
            'state' => 'Madrid',
            'zip_code' => '28046',
            'bedrooms' => 0,
            'bathrooms' => 2,
            'area' => 200.00,
            'images' => ['office1.jpg'],
            'features' => ['Estacionamiento', 'Seguridad 24/7', 'Sala de reuniones'],
            'agent_id' => 3,
            'status' => 'available',
        ]);

        \App\Models\Property::create([
            'title' => 'Chalet con piscina',
            'description' => 'Impresionante chalet de lujo con piscina privada y amplio jardín. Ubicado en zona residencial exclusiva.',
            'price' => 850000.00,
            'type' => 'sale',
            'category' => 'house',
            'address' => 'Urbanización Los Olivos 25',
            'city' => 'Pozuelo de Alarcón',
            'state' => 'Madrid',
            'zip_code' => '28223',
            'bedrooms' => 5,
            'bathrooms' => 3,
            'area' => 300.00,
            'images' => ['villa1.jpg', 'villa1_pool.jpg'],
            'features' => ['Piscina', 'Jardín', 'Garaje doble', 'Chimenea', 'Bodega'],
            'agent_id' => 2,
            'status' => 'available',
        ]);
    }
}
