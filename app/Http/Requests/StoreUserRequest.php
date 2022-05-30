<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
        // dd($this -> authorize());
        return [
            'username' => ['required'],
            'phone_number' => ['required'],
            'email' => ['required'],
            'pass' => ['required'],
        ];
    }

    public function messages()
    {
      return [
          'required' => 'username is required',
        //   'username.max' => 'username too long',
        //   'email.required' => 'An email is required',
        //   'date_Of_birth' => 'Date of Birth is required'
      ];
    }
}
