<?php

namespace App\Exceptions;

class AuthUserNotFoundException extends ApiException
{
    /**
     * Get HTTP Status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 404;
    }

    /**
     * Get internal message (it should not be displayed to user)
     *
     * @return string
     */
    public function getApiMessage()
    {
        return 'User for this token does not exists or is deleted';
    }

    /**
     * Get Api error code
     *
     * @return string
     */
    public function getApiCode()
    {
        return ErrorCode::AUTH_USER_NOT_FOUND;
    }
}
