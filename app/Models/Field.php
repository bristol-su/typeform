<?php

namespace BristolSU\Module\Typeform\Models;

use Database\Typeform\Factories\TypeformFieldsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new TypeformFieldsFactory();
    }

}
