<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Tipo de visita
            $table->enum('type', ['showing', 'inspection', 'evaluation', 'follow_up', 'closing'])->default('showing')->after('agent_id');
            
            // Prioridad
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('type');
            
            // Duración estimada y real (en minutos)
            $table->integer('estimated_duration')->default(60)->after('scheduled_at');
            $table->integer('actual_duration')->nullable()->after('estimated_duration');
            
            // Fechas adicionales
            $table->dateTime('completed_at')->nullable()->after('actual_duration');
            $table->dateTime('cancelled_at')->nullable()->after('completed_at');
            
            // Información de contacto
            $table->string('client_phone')->nullable()->after('cancelled_at');
            $table->string('client_email')->nullable()->after('client_phone');
            
            // Participantes adicionales
            $table->json('additional_participants')->nullable()->after('client_email'); // [{name, phone, role}]
            
            // Recordatorios
            $table->boolean('reminder_sent')->default(false)->after('additional_participants');
            $table->dateTime('reminder_sent_at')->nullable()->after('reminder_sent');
            $table->integer('reminder_hours_before')->default(24)->after('reminder_sent_at');
            
            // Feedback y resultados
            $table->enum('outcome', ['interested', 'not_interested', 'needs_follow_up', 'offer_made', 'deal_closed'])->nullable()->after('reminder_hours_before');
            $table->text('client_feedback')->nullable()->after('outcome');
            $table->text('agent_observations')->nullable()->after('client_feedback');
            $table->integer('client_rating')->nullable()->after('agent_observations'); // 1-5 stars
            
            // Información financiera
            $table->decimal('offered_price', 15, 2)->nullable()->after('client_rating');
            $table->text('financing_discussed')->nullable()->after('offered_price');
            
            // Archivos adjuntos
            $table->json('attachments')->nullable()->after('financing_discussed'); // Fotos, documentos, etc.
            
            // Seguimiento
            $table->boolean('requires_follow_up')->default(false)->after('attachments');
            $table->date('follow_up_date')->nullable()->after('requires_follow_up');
            $table->text('follow_up_notes')->nullable()->after('follow_up_date');
            
            // Metadatos
            $table->json('metadata')->nullable()->after('follow_up_notes'); // Información adicional flexible
            $table->string('cancellation_reason')->nullable()->after('metadata');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->after('cancellation_reason');
            
            // Índices para optimización
            $table->index(['status', 'scheduled_at']);
            $table->index(['agent_id', 'scheduled_at']);
            $table->index(['property_id', 'scheduled_at']);
            $table->index(['outcome']);
            $table->index(['requires_follow_up', 'follow_up_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['requires_follow_up', 'follow_up_date']);
            $table->dropIndex(['outcome']);
            $table->dropIndex(['property_id', 'scheduled_at']);
            $table->dropIndex(['agent_id', 'scheduled_at']);
            $table->dropIndex(['status', 'scheduled_at']);
            
            $table->dropColumn([
                'type',
                'priority',
                'estimated_duration',
                'actual_duration',
                'completed_at',
                'cancelled_at',
                'client_phone',
                'client_email',
                'additional_participants',
                'reminder_sent',
                'reminder_sent_at',
                'reminder_hours_before',
                'outcome',
                'client_feedback',
                'agent_observations',
                'client_rating',
                'offered_price',
                'financing_discussed',
                'attachments',
                'requires_follow_up',
                'follow_up_date',
                'follow_up_notes',
                'metadata',
                'cancellation_reason',
                'cancelled_by'
            ]);
        });
    }
};
