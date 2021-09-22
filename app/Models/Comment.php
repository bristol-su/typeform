<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use BristolSU\Support\Revision\HasRevisions;
use Database\Typeform\Factories\TypeformCommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes, HasRevisions, HasFactory;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new TypeformCommentFactory();
    }
}
