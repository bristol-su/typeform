<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceRepository;
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
        'activity_instance_id',
        'approved'
    ];
    
    protected $casts = [
        'submitted_at' => 'datetime',
        'approved' => 'boolean'
    ];
    
    protected $appends = [
        'activity_instance', 'submitted_by_user'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function user()
    {
        return app(\BristolSU\ControlDB\Contracts\Repositories\User::class)->getById($this->submitted_by);
    }

    public function getSubmittedByUserAttribute()
    {
        return $this->user();
    }

    public function activityInstance()
    {
        return app(ActivityInstanceRepository::class)->getById($this->activity_instance_id);
    }

    public function getActivityInstanceAttribute()
    {
        return $this->activityInstance();
    }
    
}