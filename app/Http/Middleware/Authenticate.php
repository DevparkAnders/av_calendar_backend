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
        $tokenExpired = false;

        try {
            /** @var User $user */
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            $tokenExpired = true;
        } catch (\Exception $e) {
            return ApiResponse::responseError(ErrorCode::AUTH_INVALID_TOKEN,
                401);
        }

        // token was expired, we will try to refresh it
        if ($tokenExpired) {
            try {
                $user = $this->refreshToken($request);
            } catch (TokenExpiredException $e) {
                return ApiResponse::responseError(ErrorCode::AUTH_EXPIRED_TOKEN,
                    400);
            } catch (\Exception $e) {
                return ApiResponse::responseError(ErrorCode::AUTH_INVALID_TOKEN,
                    401);
            }
        }

        // we allow authenticate only users that are not deleted
        if (!$user || $user->isDeleted()) {
            return ApiResponse::responseError(ErrorCode::AUTH_USER_NOT_FOUND,
                404);
        }

        return $next($request);
    }

    /**
     * Refreshes token when it's expired
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return User
     */
    protected function refreshToken($request)
    {
        $newToken = JWTAuth::refresh();
        $request->headers->set('JWTRefreshed', '1', true);
        $request->headers->set('Authorization', 'Bearer ' . $newToken, true);

        return JWTAuth::authenticate();
    }
}
