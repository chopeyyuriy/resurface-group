<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Auth;

class UserHelper
{
    public static function save(Request $request, $user, $role)
    {
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $middleName = $request->input('middle_name');
        $password = $request->input('password');

        $data = [
            'name' => sprintf('%s %s %s', $firstName, $middleName, $lastName),
            'email' => $request->input('email'),
        ];
        
        if (Auth::user()->hasRole('admin')) {
            $data['status'] = (int) $request->input('status');
        }

        if(!empty($password)) {
            $data['password'] = Hash::make($password);
        }

        $user->user()->updateOrCreate([
            'userable_id' => data_get($user, 'id')
        ], $data)->assignRole($role);
    }
}
