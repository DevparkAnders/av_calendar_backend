<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use Closure;
use JWTAuth;

class VerifyIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = null;
        try {
            $user = JWTAuth::setRequest($request)->parseToken()->authenticate();
        } catch (\Exception $e) {
            // we don't care about exceptions in this place
        }

        // if user is not deleted it means they are already logged
        if ($user && !$user->isDeleted()) {
            return ApiResponse::responseError(ErrorCode::AUTH_ALREADY_LOGGED,
                403);
        }

        return $next($request);
    }
}
