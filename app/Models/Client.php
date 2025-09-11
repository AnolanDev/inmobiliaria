<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'secondary_phone',
        'document_type',
        'document_number',
        'address',
        'birth_date',
        'occupation',
        'notes',
        'interest_level',
        'status',
        'profile_image',
        'attachments',
        'last_contact_date',
        'preferred_contact_method',
    ];

    protected $casts = [
        'attachments' => 'array',
        'birth_date' => 'date',
        'last_contact_date' => 'datetime',
    ];

    protected $appends = [
        'profile_image_url',
        'status_color',
        'interest_level_color',
        'full_contact',
    ];

    const DOCUMENT_TYPES = [
        'cedula' => 'Cédula de Ciudadanía',
        'cedula_extranjeria' => 'Cédula de Extranjería',
        'pasaporte' => 'Pasaporte',
        'nit' => 'NIT',
        'tarjeta_identidad' => 'Tarjeta de Identidad',
    ];

    const STATUSES = [
        'prospecto' => 'Prospecto',
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
    ];

    const INTEREST_LEVELS = [
        'low' => 'Bajo',
        'medium' => 'Medio',
        'high' => 'Alto',
    ];

    const CONTACT_METHODS = [
        'phone' => 'Teléfono',
        'email' => 'Email',
        'whatsapp' => 'WhatsApp',
        'both' => 'Teléfono y Email',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'client_property')
                    ->withPivot('interest_type', 'status', 'notes')
                    ->withTimestamps();
    }

    public function getFullContactAttribute(): string
    {
        $contacts = [];
        if ($this->phone) $contacts[] = $this->phone;
        if ($this->secondary_phone) $contacts[] = $this->secondary_phone;
        if ($this->email) $contacts[] = $this->email;
        return implode(' | ', $contacts);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'activo' => 'bg-green-100 text-green-800',
            'inactivo' => 'bg-red-100 text-red-800',
            'prospecto' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getInterestLevelColorAttribute(): string
    {
        return match($this->interest_level) {
            'high' => 'bg-red-100 text-red-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopeProspects($query)
    {
        return $query->where('status', 'prospecto');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('secondary_phone', 'like', "%{$search}%")
              ->orWhere('document_number', 'like', "%{$search}%");
        });
    }
}
