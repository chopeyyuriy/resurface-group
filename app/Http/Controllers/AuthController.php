<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        //return \Hash::make('123456');

        return view('auth.login');
    }

    public function auth(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1;
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('root'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or not active.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    
    public function setOnesignalPlayerId(Request $request)
    {
        $user = Auth::user();
        $user->onesignal = $request->player_id;
        $user->save();
    }
}
