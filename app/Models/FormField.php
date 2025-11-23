<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'label',
        'type',
        'options',
        'validation_rules',
        'is_required',
        'is_visible',
        'order',
        'placeholder',
        'help_text'
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_visible' => 'boolean'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }


    public function getValidationRulesAttribute(): array
    {
        $rules = $this->validation_rules ?? [];

        if ($this->is_required) {
            $rules[] = 'required';
        }

        return $rules;
    }
}
