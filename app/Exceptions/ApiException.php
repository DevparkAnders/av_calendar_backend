<?php

namespace App\Exceptions;

use Exception;

abstract class ApiException extends Exception
{
    /**
     * Array for validated fields messages
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Request headers that will be added to response
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Get HTTP Status code
     *
     * @return int
     */
    abstract public function getStatusCode();

    /**
     * Get internal message (it should not be displayed to user)
     *
     * @return string
     */
    abstract public function getApiMessage();

    /**
     * Get Api error code
     *
     * @return string
     */
    abstract public function getApiCode();

    /**
     * Return response data
     *
     * @return object
     */
    public function getResponseData()
    {
        return (object)[
            'status' => $this->getStatusCode(),
            'error' => (object)[
                'code' => $this->getApiCode(),
                'message' => $this->getApiMessage(),
                'fields' => (array)$this->fields,
            ],
        ];
    }

    /**
     * Set response headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
