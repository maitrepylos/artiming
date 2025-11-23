<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'code',
        'price',
        'max_participants',
        'order',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    // Vérifier si la catégorie est pleine
    public function isFullAttribute(): bool
    {
        if (!$this->max_participants) {
            return false;
        }
        return $this->registrations()->count() >= $this->max_participants;
    }

    // Nombre de places restantes
    public function getRemainingSpacesAttribute(): ?int
    {
        if (!$this->max_participants) {
            return null;
        }
        return max(0, $this->max_participants - $this->registrations()->count());
    }
}
