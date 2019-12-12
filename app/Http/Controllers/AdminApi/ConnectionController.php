<?php

namespace BristolSU\Module\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Connection;
use BristolSU\Support\Authentication\Contracts\Authentication;

class ConnectionController extends Controller
{

    public function index(Authentication $authentication)
    {
        return Connection::where('user_id', $authentication->getUser()->id())->get();
    }
    
}