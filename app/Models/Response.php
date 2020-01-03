<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\Support\Authentication\HasResource;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasResource;
    
    protected $table = 'typeform_responses';
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'form_id',
        'submitted_by',
        'submitted_at',
        'module_instance_id',
        'activity_instance_id'
    ];
    
    protected $casts = [
        'submitted_at' => 'datetime'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
    
}