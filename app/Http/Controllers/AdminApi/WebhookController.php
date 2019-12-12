<?php

namespace BristolSU\Module\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function index(Activity $activity, ModuleInstance $moduleInstance)
    {
        return Webhook::where([
            'module_instance_id' => $moduleInstance->id(),
            'tag' => Webhook::generatedTag($moduleInstance)
        ])->get();
    }
    
    public function store(Request $request, Activity $activity, ModuleInstance $moduleInstance)
    {
        $webhook = Webhook::create([
            'module_instance_id' => $moduleInstance->id(),
            'tag' => Webhook::generatedTag($moduleInstance),
            'connection_id' => $request->input('connection_id')
        ]);
        
        $webhook->link();
        
        return $webhook;
    }
    
}