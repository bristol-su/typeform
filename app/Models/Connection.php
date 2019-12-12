<?php

namespace BristolSU\Module\Typeform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Connection extends Model
{
    use SoftDeletes;

    protected $table = 'typeform_connections';
    
    protected $fillable = [
        'name', 'api_key'
    ];
    
    protected $hidden = ['api_key'];

    public function webhooks()
    {
        return $this->hasMany(Webhook::class, 'connection_id');
    }
}