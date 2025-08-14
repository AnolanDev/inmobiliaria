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
        Schema::create('email_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('email_send_id');
            $table->string('link_url'); // URL original del enlace
            $table->string('link_text')->nullable(); // Texto del enlace
            $table->string('link_position')->nullable(); // Posición en el email (header, body, footer)
            $table->timestamp('clicked_at');
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('email_send_id')->references('id')->on('email_sends')->onDelete('cascade');
            
            // Indexes
            $table->index(['email_send_id', 'clicked_at']);
            $table->index('link_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_clicks');
    }
};