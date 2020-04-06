<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Participant;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\Permissions\Contracts\PermissionTester;

class ParticipantPageController extends Controller
{

    public function index(Activity $activity, ModuleInstance $moduleInstance, PermissionTester $permissionTester)
    {
        $responses = ($permissionTester->evaluate('typeform.view-responses') ?
            Response::forResource()->with(['answers', 'answers.field', 'comments'])->get() : collect());

        return view('typeform::participant')->with('responses', $responses);
    }
    
}