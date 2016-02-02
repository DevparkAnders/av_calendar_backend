<?php

namespace App\Http\Requests;

class ResetPassword extends Request
{
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ];
    }
}
