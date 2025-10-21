<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds the necessary foreign key relationships
     * for the projects ecosystem.
     */
    public function up(): void
    {
        // Add project_id to properties table
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('agent_id')->constrained('projects')->onDelete('set null');
            $table->index('project_id');
        });

        // Modify visits table to support both properties and projects
        Schema::table('visits', function (Blueprint $table) {
            // Add project support
            $table->foreignId('project_id')->nullable()->after('property_id')->constrained('projects')->onDelete('cascade');
            
            // Make property_id nullable (visits can be for projects OR properties)
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
            $table->foreignId('property_id')->nullable()->after('id')->constrained('properties')->onDelete('cascade');
            
            // Add performance indexes
            $table->index(['project_id', 'scheduled_at']);
            $table->index(['property_id', 'project_id', 'scheduled_at']);
            
            // Add constraint to ensure either property_id OR project_id is set
            DB::statement('ALTER TABLE visits ADD CONSTRAINT visits_property_or_project_required CHECK ((property_id IS NOT NULL AND project_id IS NULL) OR (property_id IS NULL AND project_id IS NOT NULL))');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove visits constraints and relationships
        Schema::table('visits', function (Blueprint $table) {
            DB::statement('ALTER TABLE visits DROP CONSTRAINT visits_property_or_project_required');
            
            $table->dropIndex(['property_id', 'project_id', 'scheduled_at']);
            $table->dropIndex(['project_id', 'scheduled_at']);
            
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
            
            // Restore property_id as required
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
            $table->foreignId('property_id')->after('id')->constrained('properties')->onDelete('cascade');
        });

        // Remove project relationship from properties
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};