<?php

namespace App\Exceptions;

class AuthCreateTokenException extends ApiException
{
    /**
     * Get HTTP Status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 500;
    }

    /**
     * Get internal message (it should not be displayed to user)
     *
     * @return string
     */
    public function getApiMessage()
    {
        return 'Invalid user credentials';
    }

    /**
     * Get Api error code
     *
     * @return string
     */
    public function getApiCode()
    {
        return ErrorCode::AUTH_CANNOT_CREATE_TOKEN;
    }
}
