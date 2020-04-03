<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Participant;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\Permissions\Facade\PermissionTester;

class ParticipantPageController extends Controller
{

    public function index()
    {
        $responses = (PermissionTester::evaluate('typeform.view-responses') ?
            Response::forResource()->with(['answers', 'answers.field'])->get() : collect());

        return view('typeform::participant')->with('responses', $responses);
    }
    
}