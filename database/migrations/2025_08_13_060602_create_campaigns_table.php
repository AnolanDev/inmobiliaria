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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('email'); // email, sms, social, digital_ads
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->text('description')->nullable();
            $table->json('target_audience')->nullable(); // filtros de segmentación
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('spent', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('content')->nullable(); // contenido del email/mensaje
            $table->json('settings')->nullable(); // configuraciones específicas
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'type']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
