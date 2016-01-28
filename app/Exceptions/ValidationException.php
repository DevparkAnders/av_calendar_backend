<?php

namespace App\Exceptions;

class ValidationException extends ApiException
{

    /**
     * Get HTTP Status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 422;
    }

    /**
     * Get internal message (it should not be displayed to user)
     *
     * @return string
     */
    public function getApiMessage()
    {
        return 'Validation errors';
    }

    /**
     * Get Api error code
     *
     * @return string
     */
    public function getApiCode()
    {
        return ErrorCode::VALIDATION_FAILED;
    }

    /**
     * Set validation error fields
     *
     * @param array $errors
     *
     * @return $this
     */
    public function setFields(array $errors)
    {
        $this->fields = $this->formatErrors($errors);

        return $this;
    }

    /**
     * Formats validation errors
     *
     * @param array $errors
     *
     * @return array
     */
    protected function formatErrors(array $errors)
    {
        $fields = [];

        foreach ($errors as $field => $messages) {
            $fields[] = (object)[
                'name' => $field,
                'messages' => $messages,
            ];
        }

        return $fields;
    }
}
