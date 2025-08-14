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
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // single, drip, newsletter, automated
            $table->string('status')->default('draft'); // draft, scheduled, sending, sent, paused, cancelled
            
            // Template and content
            $table->unsignedBigInteger('email_template_id');
            $table->string('subject_override')->nullable(); // Override template subject
            
            // Segmentation and targeting
            $table->json('segment_conditions')->nullable(); // Condiciones para segmentar leads
            $table->json('recipient_filters')->nullable(); // Filtros adicionales
            $table->integer('estimated_recipients')->default(0);
            $table->integer('actual_recipients')->default(0);
            
            // Scheduling
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // A/B Testing
            $table->boolean('is_ab_test')->default(false);
            $table->json('ab_test_config')->nullable(); // Configuración del test A/B
            $table->unsignedBigInteger('winning_variant_id')->nullable(); // ID del template ganador
            
            // Drip campaign settings
            $table->boolean('is_drip_campaign')->default(false);
            $table->json('drip_schedule')->nullable(); // Configuración del timeline del drip
            $table->integer('drip_sequence_order')->nullable();
            $table->unsignedBigInteger('parent_drip_campaign_id')->nullable();
            
            // Performance tracking
            $table->integer('emails_sent')->default(0);
            $table->integer('emails_delivered')->default(0);
            $table->integer('emails_opened')->default(0);
            $table->integer('emails_clicked')->default(0);
            $table->integer('emails_bounced')->default(0);
            $table->integer('emails_unsubscribed')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            
            // Relations
            $table->unsignedBigInteger('marketing_campaign_id')->nullable(); // Link to marketing campaigns
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('email_template_id')->references('id')->on('email_templates');
            $table->foreign('marketing_campaign_id')->references('id')->on('campaigns')->onDelete('set null');
            $table->foreign('parent_drip_campaign_id')->references('id')->on('email_campaigns')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'type']);
            $table->index(['scheduled_at', 'status']);
            $table->index('marketing_campaign_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};