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
        Schema::create('email_sends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('email_campaign_id');
            $table->morphs('recipient'); // lead_id/client_id, lead/client
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            
            // Email content at send time (for tracking purposes)
            $table->string('subject');
            $table->longText('html_content')->nullable();
            $table->longText('text_content')->nullable();
            
            // Send status
            $table->string('status')->default('queued'); // queued, sending, sent, failed, bounced
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Tracking
            $table->boolean('opened')->default(false);
            $table->timestamp('first_opened_at')->nullable();
            $table->timestamp('last_opened_at')->nullable();
            $table->integer('open_count')->default(0);
            
            $table->boolean('clicked')->default(false);
            $table->timestamp('first_clicked_at')->nullable();
            $table->timestamp('last_clicked_at')->nullable();
            $table->integer('click_count')->default(0);
            
            $table->boolean('unsubscribed')->default(false);
            $table->timestamp('unsubscribed_at')->nullable();
            
            // Tracking tokens
            $table->string('tracking_token')->unique(); // Para tracking de apertura
            $table->string('unsubscribe_token')->unique(); // Para unsubscribe
            
            // A/B Testing
            $table->string('ab_variant')->nullable(); // A, B, control
            
            // Activity integration
            $table->unsignedBigInteger('activity_id')->nullable(); // Si se crea como actividad
            
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('email_campaign_id')->references('id')->on('email_campaigns')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
            
            // Indexes
            $table->index(['email_campaign_id', 'status']);
            $table->index('recipient_email');
            $table->index('tracking_token');
            $table->index('unsubscribe_token');
            $table->index(['sent_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sends');
    }
};