<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['Campestres', 'Urbanos', 'Turísticos'];
        $statuses = ['Disponible', 'Reservado', 'Vendido'];
        
        // Generar nombres más realistas según el tipo
        $projectNames = [
            'Campestres' => ['Hacienda', 'Finca', 'Campo', 'Villa', 'Reserva Natural'],
            'Urbanos' => ['Torres', 'Residencial', 'Plaza', 'Centro', 'Metropolitan'],
            'Turísticos' => ['Resort', 'Hotel', 'Spa', 'Marina', 'Beach Club']
        ];
        
        $type = $this->faker->randomElement($types);
        $projectType = $this->faker->randomElement($projectNames[$type]);
        
        return [
            'name' => $projectType . ' ' . $this->faker->lastName,
            'description' => $this->faker->paragraphs(2, true),
            'type' => $type,
            'status' => $this->faker->randomElement($statuses),
            'property_count' => $this->faker->numberBetween(5, 50),
            'cover_image' => '', // Se usará la imagen placeholder del modelo
            'gallery' => null,
            'videos' => null,
        ];
    }
}
