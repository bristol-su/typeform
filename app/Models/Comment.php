<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use BristolSU\Support\Revision\HasRevisions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes, HasRevisions;

    protected $table = 'typeform_comments';

    protected $fillable = [
        'comment',
        'posted_by',
        'response_id',
    ];

    public function getPostedByAttribute($postedById)
    {
        return app()->make(UserRepository::class)->getById($postedById);
    }

    public function response()
    {
        return $this->belongsTo(Response::class);
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
