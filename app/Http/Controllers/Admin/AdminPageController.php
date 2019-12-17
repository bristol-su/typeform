<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Admin;


use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\Permissions\Contracts\PermissionStore;
use BristolSU\Support\Permissions\Contracts\PermissionTester;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;

class AdminPageController extends Controller
{
    
    public function index(PermissionStore $permission, Activity $activity, ModuleInstance $moduleInstance)
    {
        $this->authorize('admin.view-form');
        
        $connector = app(ModuleInstanceServiceRepository::class)->getConnectorForService('typeform', $moduleInstance->id);
        $response = $connector->request('get', sprintf('/forms/%s/responses', settings('form_id')));
        
        
        return view(alias() . '::admin')->with('responses', $response->getBody()->getContents());
    }
    
}