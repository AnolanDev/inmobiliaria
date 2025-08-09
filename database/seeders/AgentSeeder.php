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
            'phone' => '+57 300 123 4567',
            'type' => 'Interno',
            'bio' => 'Agente inmobiliario con 5 años de experiencia en ventas residenciales. Especialista en propiedades familiares y apartamentos.',
            'facebook' => 'https://facebook.com/juan.perez.agente',
            'instagram' => 'https://instagram.com/juan_perez_inmobiliaria',
            'linkedin' => 'https://linkedin.com/in/juan-perez-inmobiliaria',
            'is_active' => true,
        ]);

        \App\Models\Agent::create([
            'name' => 'María García',
            'email' => 'maria.garcia@inmobiliaria.com',
            'phone' => '+57 301 987 6543',
            'type' => 'Externo',
            'bio' => 'Especialista en propiedades comerciales y de lujo con más de 8 años de experiencia en el sector inmobiliario.',
            'facebook' => 'https://facebook.com/maria.garcia.inmobiliaria',
            'instagram' => 'https://instagram.com/maria_garcia_properties',
            'is_active' => true,
        ]);

        \App\Models\Agent::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos.rodriguez@inmobiliaria.com',
            'phone' => '+57 302 555 1234',
            'type' => 'Interno',
            'bio' => 'Experto en alquileres y gestión de propiedades. Especializado en inversiones inmobiliarias y asesoría integral.',
            'linkedin' => 'https://linkedin.com/in/carlos-rodriguez-inmobiliario',
            'is_active' => true,
        ]);

        \App\Models\Agent::create([
            'name' => 'Ana López',
            'email' => 'ana.lopez@inmobiliaria.com',
            'phone' => '+57 303 444 7890',
            'type' => 'Externo',
            'bio' => 'Agente especializada en terrenos y desarrollos inmobiliarios. Amplia experiencia en proyectos residenciales.',
            'instagram' => 'https://instagram.com/ana_lopez_terrenos',
            'is_active' => false,
        ]);
    }
}
