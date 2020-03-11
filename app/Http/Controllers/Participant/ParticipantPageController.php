<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Participant;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Response;

class ParticipantPageController extends Controller
{

    public function index()
    {
        $this->authorize('view-form');

        $responses = Response::forResource()->with(['answers', 'answers.field'])->get();

        return view('typeform::participant')->with('responses', $responses);
    }
    
}