<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add comments to existing tables to document the new image structure
        DB::statement("ALTER TABLE projects COMMENT = 'Projects table with optimized responsive images. cover_image and gallery fields now store JSON arrays with multiple image sizes (thumbnail, medium, large, original).'");
        DB::statement("ALTER TABLE properties COMMENT = 'Properties table with optimized responsive images. cover_image and gallery fields now store JSON arrays with multiple image sizes.'");
        DB::statement("ALTER TABLE agents COMMENT = 'Agents table with optimized responsive images. profile_picture and gallery fields now store JSON arrays with multiple image sizes.'");
        DB::statement("ALTER TABLE blogs COMMENT = 'Blogs table with optimized responsive images. cover_image and gallery fields now store JSON arrays with multiple image sizes.'");
        
        // The existing JSON columns can already handle the new structure
        // No schema changes needed since we're maintaining backward compatibility
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove comments
        DB::statement("ALTER TABLE projects COMMENT = ''");
        DB::statement("ALTER TABLE properties COMMENT = ''");
        DB::statement("ALTER TABLE agents COMMENT = ''");
        DB::statement("ALTER TABLE blogs COMMENT = ''");
    }
};
