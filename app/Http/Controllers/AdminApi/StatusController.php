<?php

namespace BristolSU\Module\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\ModuleInstance;

class StatusController extends Controller
{

    public function approve(Activity $activity, ModuleInstance $moduleInstance, Response $response)
    {
        $this->authorize('admin.approve');
        $response->approved = true;
        $response->save();
        return $response;
    }

    public function reject(Activity $activity, ModuleInstance $moduleInstance, Response $response)
    {
        $this->authorize('admin.approve');
        $response->approved = false;
        $response->save();
        return $response;
    }
    
}