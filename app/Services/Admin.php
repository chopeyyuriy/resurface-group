<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function isAdmin()
    {
        $admin = User::role('admin')->where('id', Auth::id())->first();

        return ['admin' => $admin];
    }
}