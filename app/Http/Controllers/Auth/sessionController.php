<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password']
        ]);

        Auth::logoutOtherDevices($request->password);

        return back()->with('status', 'sesiones-cerradas');
    }
}