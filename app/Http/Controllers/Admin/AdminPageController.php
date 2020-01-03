<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Admin;


use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\Permissions\Contracts\PermissionStore;

class AdminPageController extends Controller
{
    
    public function index(PermissionStore $permission, Activity $activity, ModuleInstance $moduleInstance)
    {
        $this->authorize('admin.view-form');
        
        $responses = Response::where('module_instance_id', $moduleInstance->id)->with(['answers', 'answers.field'])->get();
        
        return view(alias() . '::admin')->with('responses', $responses);
    }
    
}