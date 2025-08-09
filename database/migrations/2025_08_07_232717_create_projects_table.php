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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['Campestres', 'Urbanos', 'Turísticos']);
            $table->enum('status', ['Vendido', 'Disponible', 'Reservado'])->default('Disponible');
            $table->integer('property_count')->nullable()->default(0);
            $table->string('cover_image');
            $table->json('gallery')->nullable();
            $table->json('videos')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
