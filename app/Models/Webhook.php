<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\Module\Typeform\Support\Webhooks\Contracts\WebhookLinker;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Webhook extends Model
{
    use SoftDeletes;

    protected $table = 'typeform_webhooks';
    
    protected $fillable = [
        'module_instance_id', 'tag', 'connection_id'
    ];

    public static function generatedTag(ModuleInstance $moduleInstance)
    {
        return $moduleInstance->activity->slug . '-' . $moduleInstance->slug . '-' . $moduleInstance->moduleInstanceSettings->settings['form_id'];
    }

    public function link()
    {
        app(WebhookLinker::class)->link($this);
    }

    public function moduleInstance()
    {
        return $this->belongsTo(ModuleInstance::class);
    }

    public function typeformConnection()
    {
        return $this->belongsTo(Connection::class, 'connection_id');
    }

    public function url()
    {
        return url('/a/' . $this->moduleInstance->activity->slug . '/' . $this->moduleInstance->slug . '/typeform/webhook/responses');
    }
    
}