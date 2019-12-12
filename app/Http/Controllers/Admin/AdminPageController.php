<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Admin;


use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Support\Permissions\Contracts\PermissionStore;
use BristolSU\Support\Permissions\Contracts\PermissionTester;

class AdminPageController extends Controller
{
    
    public function index(PermissionStore $permission)
    {
        $this->authorize('admin.view-form');
        return view(alias() . '::admin');
    }
    
}