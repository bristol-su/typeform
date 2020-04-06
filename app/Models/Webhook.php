<?php

namespace BristolSU\Module\Typeform\Models;

use BristolSU\Support\ModuleInstance\Contracts\ModuleInstanceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a webhook
 * 
 * @method static Builder fromModuleInstance(ModuleInstance $moduleInstance) Retrieve a webhook from a module instance
 */
class Webhook extends Model
{
    use SoftDeletes;

    protected $table = 'typeform_webhooks';

    protected $fillable = [
        'module_instance_id', 'tag', 'form_id'
    ];

    public function scopeFromModuleInstance(Builder $query, ModuleInstance $moduleInstance)
    {
        $query->where([
            'form_id' => $moduleInstance->setting('form_id'),
            'module_instance_id' => $moduleInstance->id(),
            'tag' => static::generatedTag($moduleInstance)
        ]);
    }

    /**
     * Create a tag to refer to the webhook as. This will always return the same value given the same module instance and webhook.
     * @param ModuleInstance $moduleInstance
     * @return string
     */
    public static function generatedTag(ModuleInstance $moduleInstance)
    {
        return app()->environment() . '-' . $moduleInstance->activity->slug . '-' . $moduleInstance->slug . '-' . $moduleInstance->setting('form_id');
    }

    /**
     * Get the URL to send responses to
     * 
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function url()
    {
        return url('/api/a/' . $this->moduleInstance()->activity->slug . '/' . $this->moduleInstance()->slug . '/typeform/webhook/responses');
    }

    /**
     * Get the module instance 
     * 
     * @return \BristolSU\Support\ModuleInstance\Contracts\ModuleInstance
     */
    public function moduleInstance()
    {
        return app(ModuleInstanceRepository::class)->getById($this->module_instance_id);
    }

    
}