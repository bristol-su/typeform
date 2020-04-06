<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Admin;


use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\ActivityInstance\ActivityInstance;

class AdminPageController extends Controller
{
    
    public function index()
    {
        $this->authorize('admin.view-form');
        
        $responses = Response::forModuleInstance()->with(['answers', 'answers.field', 'comments'])->get();

        return view('typeform::admin')->with('responses', $responses);
    }
    
}