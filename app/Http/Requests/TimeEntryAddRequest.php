<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeEntryAddRequest extends FormRequest
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
        return [
            'date' => 'required',
            'time' => ['required', 'regex:/^[0-9][0-9]:[0-9][0-9]$/'],
            'clinicians' => 'required',
            'client' => 'required|exists:clients,id',
            'activity_type' => 'required',
            'notes' => 'nullable|max:250',
        ];
    }
}
