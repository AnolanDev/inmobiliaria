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
        Schema::table('projects', function (Blueprint $table) {
            // Add sort_order column if it doesn't exist
            if (!Schema::hasColumn('projects', 'sort_order')) {
                $table->integer('sort_order')->nullable()->after('property_count');
            }
            
            // Change cover_image to LONGTEXT to support optimized image JSON
            $table->longText('cover_image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            
            // Revert to original varchar(255)
            $table->string('cover_image', 255)->nullable()->change();
        });
    }
};