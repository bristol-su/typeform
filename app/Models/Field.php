<?php

namespace BristolSU\Module\Typeform\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected $table = 'typeform_fields';

    protected $fillable = [
        'form_id', 'id', 'type', 'title'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

}