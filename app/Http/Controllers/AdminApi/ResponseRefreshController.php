<?php

namespace BristolSU\Module\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Jobs\UpdateResponses;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\ModuleInstance;

class ResponseRefreshController extends Controller
{

    public function refresh(Activity $activity, ModuleInstance $moduleInstance)
    {
        $this->authorize('admin.refresh-form-responses');
        
        dispatch_now(new UpdateResponses($moduleInstance));
    }
    
}
