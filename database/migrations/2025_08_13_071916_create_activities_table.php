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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // call, email, meeting, note, task, sms, whatsapp
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->timestamp('scheduled_at')->nullable(); // para actividades programadas
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration')->nullable(); // duración en minutos
            $table->json('metadata')->nullable(); // datos adicionales específicos por tipo
            
            // Relaciones polimórficas - una actividad puede ser para lead, client, property, etc.
            $table->morphs('related'); // related_type, related_id
            
            // Usuario que realiza la actividad
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Usuario asignado (puede ser diferente al que crea)
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Recordatorios
            $table->boolean('has_reminder')->default(false);
            $table->timestamp('reminder_at')->nullable();
            $table->boolean('reminder_sent')->default(false);
            
            // Seguimiento
            $table->boolean('is_follow_up')->default(false);
            $table->foreignId('parent_activity_id')->nullable()->constrained('activities')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para optimizar consultas (morphs ya crea el índice related_type, related_id)
            $table->index(['type', 'status']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['reminder_at', 'reminder_sent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};