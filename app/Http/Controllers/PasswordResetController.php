<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function showReset($token)
    {
        return view('pages.showreset', compact('token'));
    }
}
