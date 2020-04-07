<?php

namespace BristolSU\Module\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Typeform\Events\ResponseApproved;
use BristolSU\Module\Typeform\Events\ResponseRejected;
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
        $response->refresh();
        
        event(new ResponseApproved($response));
        
        return $response;
    }

    public function reject(Activity $activity, ModuleInstance $moduleInstance, Response $response)
    {
        $this->authorize('admin.approve');
        $response->approved = false;
        $response->save();
        $response->refresh();
        
        event(new ResponseRejected($response));

        return $response;
    }
    
}