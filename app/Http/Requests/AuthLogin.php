<?php

namespace App\Http\Requests;

class AuthLogin extends Request
{
    public function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required',
        ];
    }
}
