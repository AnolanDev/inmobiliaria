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
        Schema::table('agents', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
            $table->enum('type', ['Interno', 'Externo'])->default('Interno')->after('phone');
            $table->string('facebook')->nullable()->after('bio');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('linkedin')->nullable()->after('instagram');
            $table->json('gallery')->nullable()->after('linkedin');
            $table->json('videos')->nullable()->after('gallery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            $table->dropColumn(['type', 'facebook', 'instagram', 'linkedin', 'gallery', 'videos']);
        });
    }
};