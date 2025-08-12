<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $action = fake()->randomElement(['view', 'create', 'edit', 'delete']);
        $module = fake()->randomElement(['users', 'properties', 'roles', 'clients', 'agents']);
        $name = ucfirst($action) . ' ' . ucfirst($module);
        $slug = $module . '-' . $action;
        
        return [
            'name' => $name,
            'slug' => $slug,
            'module' => $module,
            'action' => $action,
            'description' => 'Permite ' . $action . ' en el módulo de ' . $module,
        ];
    }
}
