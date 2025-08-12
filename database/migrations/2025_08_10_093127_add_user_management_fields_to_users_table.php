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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email_verified_at');
            $table->string('phone')->nullable()->after('avatar');
            $table->string('position')->nullable()->after('phone'); // Job title/position
            $table->text('bio')->nullable()->after('position'); // Short biography
            $table->boolean('is_active')->default(true)->after('bio');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->json('settings')->nullable()->after('last_login_ip'); // User preferences
            $table->json('metadata')->nullable()->after('settings'); // Additional user info
            $table->timestamp('invited_at')->nullable()->after('metadata');
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null')->after('invited_at');
            $table->timestamp('password_changed_at')->nullable()->after('invited_by');
            $table->boolean('force_password_change')->default(false)->after('password_changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invited_by');
            $table->dropColumn([
                'avatar',
                'phone',
                'position',
                'bio',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'settings',
                'metadata',
                'invited_at',
                'password_changed_at',
                'force_password_change'
            ]);
        });
    }
};