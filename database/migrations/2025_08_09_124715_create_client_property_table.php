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
        Schema::create('client_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->enum('interest_type', ['compra', 'arriendo', 'inversion'])->default('compra');
            $table->enum('status', ['interesado', 'contactado', 'visitado', 'negociando', 'cerrado', 'descartado'])->default('interesado');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['client_id', 'property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_property');
    }
};
