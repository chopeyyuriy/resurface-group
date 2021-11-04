<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('admins');
    }

    public function table()
    {
        $user = User::role('admin')->get();

        return datatables()->of($user)
            ->addColumn('action', function ($user) {
                return '<ul class="list-inline font-size-20 contact-links mb-0">
                    <li class="list-inline-item pe-2"><a href="' . route('users.form', ['id' => $user->id]) . '" title="Edit"><i class="mdi mdi-circle-edit-outline"></i></a></li>
                    <li class="list-inline-item px-2"><a href="' . route('users.delete', ['id' => $user->id]) . '" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="mdi mdi-close-circle-outline"></i></a></li>
                </ul>';
            })
            ->editColumn('status', function ($user) {
                return config('client.status.' . data_get($user, 'status', 0), 'Unactive');
            })->make(true);
    }

    public function form(int $id = 0)
    {
        $user = User::find($id);
        return view('admin-profile', compact('user'));
    }

    public function save(AdminProfileRequest $request, int $id)
    {
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'status' => (int) $request->input('status'),
            'userable_type' => '',
            'userable_id' => 0
        ];

        if(!empty($request->input('password'))) {
            $data['password'] = Hash::make($request->input('password'));
        }

        User::updateOrCreate([
            'id' => $id
        ],$data)->assignRole('admin');

        if($id > 0) {
            $status = 'Profile updated';
        } else {
            $status = 'Profile created';
        }

        return redirect(route('users'))->with('status', $status);
    }

    public function delete(int $id)
    {
        $user = User::find($id);

        if(!empty($user)) {
            $user->delete();
        }

        return redirect(route('users'))->with('status', 'User deleted');
    }
}
