<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientUpdateRequest extends FormRequest
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

        return [
            'first_name' => ['required', 'max:64'],
            'last_name' => ['required', 'max:64'],
            'middle_name' => ['nullable', 'max:64'],
            'date_birth' => ['required', 'date'],
            'admission_date' => ['required', 'date'],
            'referred_name' => ['max:64'],
            'referred_company' => ['max:128'],
            'referred_phone' => ['nullable', 'max:255'],
            'referred_email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'max:255'],
            'city' => ['required', 'max:255'],
            'zipcode' => ['required', 'numeric', 'max:99999'],
            'email' => ['required', 'email', Rule::unique('clients', 'email')->ignore($id)],
            
            'relationship_status' => ['required'],
            'marital_status' => ['required'],
            'gender' => ['required'],
            'race' => ['required'],
        ];
    }
}
