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

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
