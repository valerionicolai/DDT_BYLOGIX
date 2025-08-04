<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

Route::get('/debug-session', function (Request $request) {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'auth_check' => Auth::check(),
        'auth_user' => Auth::user(),
        'auth_guard' => Auth::getDefaultDriver(),
        'web_guard_check' => Auth::guard('web')->check(),
        'web_guard_user' => Auth::guard('web')->user(),
        'request_headers' => $request->headers->all(),
        'cookies' => $request->cookies->all(),
    ]);
});