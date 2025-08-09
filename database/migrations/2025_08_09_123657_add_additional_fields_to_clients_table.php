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
        Schema::table('clients', function (Blueprint $table) {
            // Documento de identidad
            $table->string('document_type')->default('cedula')->after('name'); // cedula, pasaporte, nit, etc.
            $table->string('document_number')->nullable()->after('document_type');
            
            // Dirección completa
            $table->text('address')->nullable()->after('phone');
            
            // Estado del cliente
            $table->enum('status', ['activo', 'inactivo', 'prospecto'])->default('prospecto')->after('interest_level');
            
            // Foto de perfil
            $table->string('profile_image')->nullable()->after('status');
            
            // Archivos adjuntos (JSON para múltiples archivos)
            $table->json('attachments')->nullable()->after('profile_image');
            
            // Información adicional
            $table->date('birth_date')->nullable()->after('attachments');
            $table->string('occupation')->nullable()->after('birth_date');
            $table->string('secondary_phone')->nullable()->after('occupation');
            
            // Metadatos
            $table->timestamp('last_contact_date')->nullable()->after('secondary_phone');
            $table->string('preferred_contact_method')->default('phone')->after('last_contact_date'); // phone, email, whatsapp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'document_type',
                'document_number', 
                'address',
                'status',
                'profile_image',
                'attachments',
                'birth_date',
                'occupation',
                'secondary_phone',
                'last_contact_date',
                'preferred_contact_method'
            ]);
        });
    }
};
