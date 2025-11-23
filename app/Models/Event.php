<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'event_date',
        'is_active',
        'logo',
        'settings'
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    // Générer automatiquement le slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name);
            }
        });

    }



    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('order');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    // Scope pour événements actifs uniquement
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // URL publique du formulaire
    public function getPublicUrlAttribute()
    {
        return route('event.register', $this->slug);
    }
}
