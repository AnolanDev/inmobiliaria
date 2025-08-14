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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->longText('html_content');
            $table->longText('text_content')->nullable();
            $table->json('variables')->nullable(); // Variables disponibles para personalización
            $table->string('category')->default('general'); // bienvenida, seguimiento, newsletter, etc.
            $table->string('status')->default('draft'); // draft, active, archived
            $table->boolean('is_system_template')->default(false); // Templates del sistema vs personalizados
            $table->json('metadata')->nullable(); // Configuraciones adicionales
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['status', 'category']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};