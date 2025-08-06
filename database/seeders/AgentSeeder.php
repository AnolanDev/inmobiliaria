<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Agent::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@inmobiliaria.com',
            'phone' => '+34 123 456 789',
            'bio' => 'Agente inmobiliario con 5 años de experiencia en ventas residenciales.',
            'is_active' => true,
        ]);

        \App\Models\Agent::create([
            'name' => 'María García',
            'email' => 'maria.garcia@inmobiliaria.com',
            'phone' => '+34 987 654 321',
            'bio' => 'Especialista en propiedades comerciales y de lujo.',
            'is_active' => true,
        ]);

        \App\Models\Agent::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos.rodriguez@inmobiliaria.com',
            'phone' => '+34 555 123 456',
            'bio' => 'Experto en alquileres y gestión de propiedades.',
            'is_active' => true,
        ]);
    }
}
