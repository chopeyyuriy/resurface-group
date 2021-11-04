<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth;

class ClinicianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->segment(3);

        $rule = [
            'first_name' => ['required', 'max:64'],
            'middle_name' => ['nullable', 'max:64'],
            'last_name' => ['required', 'max:64'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id, 'userable_id')],
        ];
        
        if (Auth::user()->hasRole('admin')) {
            $rule['location'] = ['required'];
        }

        if(empty($id)) {
            $rule['password'] = ['required', 'min:' . env('MIN_PASSW'), 'max:' . env('MAX_PASSW'), 'confirmed'];
        } elseif(!empty($id) && request('password'))  {
            $rule['password'] = ['min:' . env('MIN_PASSW'), 'max:' . env('MAX_PASSW'), 'confirmed'];
        }

        return $rule;
    }
}
