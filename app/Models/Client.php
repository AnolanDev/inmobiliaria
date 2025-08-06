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
        'notes',
        'interest_level',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
