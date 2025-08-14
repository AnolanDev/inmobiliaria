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
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('unsubscribed')->default(false)->after('notes');
            $table->timestamp('unsubscribed_at')->nullable()->after('unsubscribed');
            $table->string('unsubscribe_reason')->nullable()->after('unsubscribed_at');
            
            // Add index for better performance on email marketing queries
            $table->index(['unsubscribed', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['unsubscribed', 'deleted_at']);
            $table->dropColumn(['unsubscribed', 'unsubscribed_at', 'unsubscribe_reason']);
        });
    }
};