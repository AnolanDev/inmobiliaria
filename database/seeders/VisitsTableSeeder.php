<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Visit;
use App\Models\Property;
use App\Models\Project;
use App\Models\Client;
use App\Models\Agent;
use Carbon\Carbon;

class VisitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing entities
        $properties = Property::all();
        $projects = Project::all();
        $clients = Client::all();
        $agents = Agent::all();

        if (($properties->isEmpty() && $projects->isEmpty()) || $clients->isEmpty() || $agents->isEmpty()) {
            $this->command->warn('No hay suficientes propiedades/proyectos, clientes o agentes para crear visitas de ejemplo.');
            return;
        }

        $types = ['showing', 'inspection', 'evaluation', 'follow_up', 'closing'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['scheduled', 'completed', 'cancelled', 'no_show'];
        $outcomes = ['interested', 'not_interested', 'needs_follow_up', 'offer_made', 'deal_closed'];

        // Create sample visits
        for ($i = 1; $i <= 50; $i++) {
            // Randomly choose between property or project (50/50)
            $isPropertyVisit = rand(0, 1) && !$properties->isEmpty();
            
            $property = null;
            $project = null;
            
            if ($isPropertyVisit) {
                $property = $properties->random();
            } elseif (!$projects->isEmpty()) {
                $project = $projects->random();
            } else {
                // Fallback to property if no projects available
                $property = $properties->random();
            }
            
            $client = $clients->random();
            $agent = $agents->random();
            
            // Random date in the past, present, or future
            $baseDate = Carbon::now();
            $scheduledAt = $baseDate->copy()->addDays(rand(-30, 30))->addHours(rand(8, 18))->minute(0)->second(0);
            
            $status = fake()->randomElement($statuses);
            $type = fake()->randomElement($types);
            $priority = fake()->randomElement($priorities);
            
            // Determine if completed and set completion data
            $completedAt = null;
            $cancelledAt = null;
            $actualDuration = null;
            $outcome = null;
            $clientRating = null;
            $clientFeedback = null;
            $agentObservations = null;
            $offeredPrice = null;
            $financingDiscussed = null;
            $cancellationReason = null;
            
            if ($status === 'completed') {
                $completedAt = $scheduledAt->copy()->addMinutes(rand(30, 120));
                $actualDuration = rand(30, 180);
                $outcome = fake()->randomElement($outcomes);
                $clientRating = rand(1, 5);
                
                if (rand(0, 1)) {
                    $clientFeedback = fake()->sentence(rand(10, 25));
                }
                
                if (rand(0, 1)) {
                    $agentObservations = fake()->sentence(rand(8, 20));
                }
                
                if ($outcome === 'offer_made' || $outcome === 'deal_closed') {
                    if ($property && $property->price) {
                        $offeredPrice = $property->price * (rand(85, 105) / 100);
                    }
                }
                
                if (rand(0, 1)) {
                    $financingDiscussed = fake()->sentence(rand(5, 15));
                }
            } elseif ($status === 'cancelled') {
                $cancelledAt = $scheduledAt->copy()->subDays(rand(1, 5));
                $cancellationReason = fake()->randomElement([
                    'Cliente canceló por motivos personales',
                    'Problema con la disponibilidad del agente',
                    'Propiedad ya vendida',
                    'Cliente encontró otra opción',
                    'Condiciones climáticas adversas',
                    'Emergencia familiar'
                ]);
            }
            
            // Additional participants (sometimes)
            $additionalParticipants = [];
            if (rand(0, 100) < 30) { // 30% chance
                $additionalParticipants[] = [
                    'name' => fake()->name(),
                    'phone' => fake()->phoneNumber(),
                    'role' => fake()->randomElement(['Cónyuge', 'Asesor Financiero', 'Familiar', 'Socio', 'Abogado'])
                ];
            }
            
            // Follow-up (sometimes)
            $requiresFollowUp = false;
            $followUpDate = null;
            $followUpNotes = null;
            
            if ($status === 'completed' && $outcome === 'needs_follow_up') {
                $requiresFollowUp = true;
                $followUpDate = Carbon::now()->addDays(rand(1, 14));
                $followUpNotes = fake()->sentence(rand(8, 15));
            }
            
            Visit::create([
                'property_id' => $property?->id,
                'project_id' => $project?->id,
                'client_id' => $client->id,
                'agent_id' => $agent->id,
                'type' => $type,
                'priority' => $priority,
                'scheduled_at' => $scheduledAt,
                'estimated_duration' => rand(30, 120),
                'actual_duration' => $actualDuration,
                'completed_at' => $completedAt,
                'cancelled_at' => $cancelledAt,
                'status' => $status,
                'client_phone' => rand(0, 1) ? $client->phone : fake()->phoneNumber(),
                'client_email' => rand(0, 1) ? $client->email : fake()->email(),
                'additional_participants' => empty($additionalParticipants) ? null : $additionalParticipants,
                'reminder_sent' => $status !== 'scheduled' ? true : fake()->boolean(70),
                'reminder_sent_at' => $status !== 'scheduled' ? $scheduledAt->copy()->subHours(24) : null,
                'reminder_hours_before' => fake()->randomElement([1, 2, 4, 8, 24, 48]),
                'outcome' => $outcome,
                'client_feedback' => $clientFeedback,
                'agent_observations' => $agentObservations,
                'client_rating' => $clientRating,
                'offered_price' => $offeredPrice,
                'financing_discussed' => $financingDiscussed,
                'requires_follow_up' => $requiresFollowUp,
                'follow_up_date' => $followUpDate,
                'follow_up_notes' => $followUpNotes,
                'notes' => rand(0, 1) ? fake()->paragraph(rand(2, 4)) : null,
                'cancellation_reason' => $cancellationReason,
                'cancelled_by' => $status === 'cancelled' ? 1 : null, // Assuming user ID 1 exists
            ]);
        }

        $this->command->info('Se crearon 50 visitas de ejemplo exitosamente.');
    }
}