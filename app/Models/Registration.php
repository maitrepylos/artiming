<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    protected $fillable = [
        'event_id',
        'category_id',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'nationalite',
        'club',
        'bib_number',
        'is_paid',
        'status',
        'code_uci',
        'email',
        'phone'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'is_paid' => 'boolean'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function customData(): HasMany
    {
        return $this->hasMany(RegistrationData::class);
    }

    // Calculer l'âge
    public function getAgeAttribute(): int
    {
        return $this->date_naissance->age;
    }

    // Nom complet
    public function getFullNameAttribute(): string
    {
        return "{$this->nom} {$this->prenom}";
    }

    // Scope pour recherche
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
                ->orWhere('prenom', 'like', "%{$term}%")
                ->orWhere('bib_number', 'like', "%{$term}%");
        });
    }

    /**
     * Récupérer une donnée personnalisée par nom de champ
     */
    public function getCustomField(string $fieldName): ?string
    {
        $data = $this->customData()
            ->whereHas('formField', function($q) use ($fieldName) {
                $q->where('name', $fieldName);
            })
            ->first();

        return $data?->value;
    }
}
