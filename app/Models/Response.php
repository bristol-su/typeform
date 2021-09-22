<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceRepository;
use BristolSU\Support\Authentication\HasResource;
use Database\Typeform\Factories\TypeformResponsesFactory;
use Database\Typeform\Factories\TypeformWebhookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasResource, HasFactory;

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

    public function comments()
    {
        return $this->hasMany(Comment::class);
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
        return new TypeformResponsesFactory();
    }

}
