<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model
{
    use SoftDeletes;

    protected $table = 'typeform_webhooks';

    protected $fillable = [
        'module_instance_id', 'tag', 'form_id'
    ];

    public function moduleInstance()
    {
        return $this->belongsTo(ModuleInstance::class);
    }

    public function scopeFromModuleInstance(Builder $query, ModuleInstance $moduleInstance)
    {
        $query->where([
            'form_id' => $moduleInstance->moduleInstanceSettings()->where('key', 'form_id')->firstOrFail()->value,
            'module_instance_id' => $moduleInstance->id(),
            'tag' => static::generatedTag($moduleInstance)
        ]);
    }
    
    public static function generatedTag(ModuleInstance $moduleInstance)
    {
        return $moduleInstance->activity->slug . '-' . $moduleInstance->slug . '-' . $moduleInstance->moduleInstanceSettings()->where('key', 'form_id')->firstOrFail()->value;
    }
    
    public function url()
    {
        return url('/api/a/' . $this->moduleInstance->activity->slug . '/' . $this->moduleInstance->slug . '/typeform/webhook/responses');
    }

    
}