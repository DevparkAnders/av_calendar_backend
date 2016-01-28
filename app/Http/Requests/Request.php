<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * By default we authorize all requests
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * If validation fails we want to throw custom exception with errors
     *
     * @param array $errors
     *
     * @return \Symfony\Component\HttpFoundation\Response|void
     * @throws ValidationException
     */
    public function response(array $errors)
    {
        throw (new ValidationException())->setFields($errors);
    }
}
