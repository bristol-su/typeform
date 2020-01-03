<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\Support\Authentication\HasResource;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'typeform_answers';

    protected $fillable = [
        'field_id', 'response_id', 'type', 'answer'
    ];

    public function setAnswerAttribute($answer)
    {
        if(is_array($answer)) {
            $this->attributes['answer'] = json_encode($answer);
            $this->attributes['encoded'] = true;
        } else {
            $this->attributes['answer'] = $answer;
        }
    }

    public function getAnswerAttribute()
    {
        if(($this->attributes['encoded']??false)) {
            return json_decode($this->attributes['answer']);
        }
        return $this->attributes['answer'];
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function response()
    {
        return $this->belongsTo(Response::class);
    }
    
}