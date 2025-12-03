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
        'locale',
        'logo',
        'settings'
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    // Langues disponibles
    public static $availableLocales = [
        'fr' => '🇫🇷 Français',
        'en' => '🇬🇧 English',
        'nl' => '🇳🇱 Nederlands',
        'de' => '🇩🇪 Deutsch',
    ];

    // Générer automatiquement le slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name);
            }
            // Définir la locale par défaut si non spécifiée
            if (empty($event->locale)) {
                $event->locale = config('app.locale', 'fr');
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

    // Obtenir le nom de la locale
    public function getLocaleNameAttribute()
    {
        return self::$availableLocales[$this->locale] ?? $this->locale;
    }

    // Appliquer la locale de l'événement
    public function applyLocale()
    {
        app()->setLocale($this->locale);
    }
}
