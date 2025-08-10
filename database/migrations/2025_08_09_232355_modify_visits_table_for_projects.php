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
            // Agregar soporte para proyectos
            $table->foreignId('project_id')->nullable()->after('property_id')->constrained()->onDelete('cascade');
            
            // Cambiar property_id a nullable ya que ahora puede ser proyecto O propiedad
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
            $table->foreignId('property_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            
            // Agregar índices adicionales
            $table->index(['project_id', 'scheduled_at']);
            $table->index(['property_id', 'project_id', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['property_id', 'project_id', 'scheduled_at']);
            $table->dropIndex(['project_id', 'scheduled_at']);
            
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
            
            // Revertir property_id a required
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
            $table->foreignId('property_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
