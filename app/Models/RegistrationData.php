<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationData extends Model
{
    protected $fillable = [
        'registration_id',
        'form_field_id',
        'value'
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function formField()
    {
        return $this->belongsTo(FormField::class);
    }
}
