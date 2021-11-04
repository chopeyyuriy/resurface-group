<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function view()
    {
        return view('auth-register');
    }

    public function save(RegistrationRequest $request)
    {
        $admin = User::create([
            'name' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'status' => 1,
            'userable_type' => '',
            'userable_id' => 0
        ])->assignRole('admin');

        Auth::login($admin, true);
        return redirect()->intended(route('root'))->with('status', 'You are Logged in as Admin');
    }
}
