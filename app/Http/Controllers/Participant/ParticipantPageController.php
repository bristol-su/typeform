<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Participant;

use BristolSU\Module\Typeform\Http\Controllers\Controller;

class ParticipantPageController extends Controller
{

    public function index()
    {
        $this->authorize('view-form');
        
        return view(alias() . '::participant');
    }
    
}