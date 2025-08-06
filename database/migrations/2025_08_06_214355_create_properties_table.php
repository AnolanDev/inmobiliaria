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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 15, 2);
            $table->enum('type', ['sale', 'rent']);
            $table->enum('category', ['house', 'apartment', 'office', 'land', 'commercial']);
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code')->nullable();
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->decimal('area', 8, 2);
            $table->json('images')->nullable();
            $table->json('features')->nullable();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['available', 'sold', 'rented', 'pending'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
