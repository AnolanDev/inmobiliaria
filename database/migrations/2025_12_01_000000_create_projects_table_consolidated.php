<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This consolidated migration creates the projects table with all fields
     * in their final state, replacing multiple incremental migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // Basic project information
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['Campestres', 'Urbanos', 'Turísticos']);
            $table->enum('status', ['Vendido', 'Disponible', 'Reservado'])->default('Disponible');
            
            // Project metrics and settings
            $table->integer('property_count')->nullable()->default(0);
            $table->boolean('is_public')->default(false);
            $table->integer('sort_order')->nullable();
            
            // Location fields
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            
            // Multimedia fields (optimized for responsive images)
            $table->longText('cover_image')->nullable()->comment('JSON array with multiple image sizes (thumbnail, medium, large, original)');
            $table->json('gallery')->nullable()->comment('Array of JSON objects with multiple image sizes for each gallery item');
            $table->json('videos')->nullable()->comment('Array of video file paths');
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['type', 'status']);
            $table->index(['city', 'state']);
            $table->index(['is_public', 'sort_order']);
            $table->index('sort_order');
        });

        // Add table comment for documentation
        DB::statement("ALTER TABLE projects COMMENT = 'Projects table with optimized responsive images. Multimedia fields store JSON arrays with multiple image sizes for responsive display.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};