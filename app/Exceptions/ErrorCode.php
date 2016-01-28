<?php

namespace App\Exceptions;

class ErrorCode
{
    // GENERAL
    const VALIDATION_FAILED = 'general.validation_failed';
    const REQUESTS_RATE_EXCEEDED = 'general.request_rate_exceeded';
    
    // AUTH
    const AUTH_INVALID_LOGIN_DATA = 'auth.invalid_login_data';
    const AUTH_CANNOT_CREATE_TOKEN = 'auth.cannot_create_token';
    const AUTH_INVALID_TOKEN = 'auth.invalid_token';
    const AUTH_EXPIRED_TOKEN = 'auth.expired_token';
    const AUTH_USER_NOT_FOUND = 'auth.user_not_found';
    const AUTH_ALREADY_LOGGED = 'auth.user_already_logged';
}
