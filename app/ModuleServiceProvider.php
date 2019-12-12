<?php

namespace BristolSU\Module\Typeform;

use BristolSU\Module\Typeform\Commands\CreateWebhooks;
use BristolSU\Module\Typeform\Support\Webhooks\Contracts\WebhookLinker as WebhookLinkerContract;
use BristolSU\Module\Typeform\Support\Webhooks\WebhookLinker;
use BristolSU\Support\Module\ModuleServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use WATR\Typeform;

class ModuleServiceProvider extends ServiceProvider
{

    protected $permissions = [
        'view-form' => [
            'name' => 'View Form',
            'description' => 'View the form.',
            'admin' => false
        ],
        'admin.view-form' => [
            'name' => 'View Form',
            'description' => 'View the form.',
            'admin' => true
        ]
    ];

    protected $events = [
    ];
    
    protected $commands = [
        CreateWebhooks::class
    ];
    
    public function alias(): string
    {
        return 'typeform';
    }

    public function namespace()
    {
        return '\BristolSU\Module\Typeform\Http\Controllers';
    }
    
    public function baseDirectory()
    {
        return __DIR__ . '/..';
    }

    public function register()
    {
        parent::register();
        
        $this->app->bind(WebhookLinkerContract::class, WebhookLinker::class);
    }

    public function boot()
    {
        parent::boot();
        
        Route::bind($this->alias() . '_file', function($id) {
            return File::findOrFail($id);
        });
    }
    
}
