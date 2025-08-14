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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'lost'])->default('new');
            $table->enum('source', ['website', 'social', 'referral', 'campaign', 'phone', 'walk_in', 'other'])->default('website');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->text('notes')->nullable();
            $table->json('interests')->nullable(); // tipos de propiedades de interés
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->string('preferred_location')->nullable();
            $table->json('contact_preferences')->nullable(); // email, phone, whatsapp
            $table->timestamp('last_contact_date')->nullable();
            $table->timestamp('next_follow_up')->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->foreignId('converted_client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'priority']);
            $table->index(['source', 'created_at']);
            $table->index(['next_follow_up']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
