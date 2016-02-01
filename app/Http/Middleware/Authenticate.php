<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Models\User;
use Closure;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use JWTAuth;

class Authenticate
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
        try {
            /** @var User $user */
            $user = JWTAuth::setRequest($request)->parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return ApiResponse::responseError(ErrorCode::AUTH_EXPIRED_TOKEN,
                400);
        } catch (\Exception $e) {
            return ApiResponse::responseError(ErrorCode::AUTH_INVALID_TOKEN,
                401);
        }
        
        // we allow authenticate only users that are not deleted
        if (!$user || $user->isDeleted()) {
            return ApiResponse::responseError(ErrorCode::AUTH_USER_NOT_FOUND,
                404);
        }

        return $next($request);
    }
}
