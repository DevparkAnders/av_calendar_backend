<?php

namespace App\Http\Requests;

class SendResetEmail extends Request
{
    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'url' => ['required'],
        ];
    }
}
