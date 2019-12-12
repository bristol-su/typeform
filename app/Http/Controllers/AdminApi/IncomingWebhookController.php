<?php

namespace BristolSU\Module\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IncomingWebhookController extends Controller
{


    public function store(Request $request)
    {
        Log::info($request->all());
    }
}