<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use Closure;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use JWTAuth;

class RefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     * @internal param null|string $guard
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        try {
            $newToken = JWTAuth::parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return ApiResponse::responseError(ErrorCode::AUTH_EXPIRED_TOKEN,
                400);
        } catch (\Exception $e) {
            return ApiResponse::responseError(ErrorCode::AUTH_INVALID_TOKEN,
                401);
        }

        $response->headers->set('Authorization', 'Bearer ' . $newToken);

        return $response;
    }
}
