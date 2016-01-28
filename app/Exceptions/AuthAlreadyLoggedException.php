<?php

namespace App\Exceptions;

class AuthAlreadyLoggedException extends ApiException
{
    /**
     * Get HTTP Status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 403;
    }

    /**
     * Get internal message (it should not be displayed to user)
     *
     * @return string
     */
    public function getApiMessage()
    {
        return 'User is already logged';
    }

    /**
     * Get Api error code
     *
     * @return string
     */
    public function getApiCode()
    {
        return ErrorCode::AUTH_ALREADY_LOGGED;
    }
}
