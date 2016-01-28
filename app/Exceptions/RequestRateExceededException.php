<?php

namespace App\Exceptions;

class RequestRateExceededException extends ApiException
{
    /**
     * Get HTTP Status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 429;
    }

    /**
     * Get internal message (it should not be displayed to user)
     *
     * @return string
     */
    public function getApiMessage()
    {
        return 'Too many requests attempts';
    }

    /**
     * Get Api error code
     *
     * @return string
     */
    public function getApiCode()
    {
        return ErrorCode::REQUESTS_RATE_EXCEEDED;
    }
}
