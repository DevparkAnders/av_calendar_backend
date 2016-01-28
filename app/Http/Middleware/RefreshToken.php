<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthInvalidTokenException;
use App\Exceptions\AuthTokenExpiredException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JwtAuth;

class RefreshToken
{
    /**
     * @var JwtAuth
     */
    protected $guard;

    /**
     * RefreshToken constructor.
     *
     * @param JwtAuth $guard
     */
    public function __construct(JwtAuth $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     * @throws AuthInvalidTokenException
     * @throws AuthTokenExpiredException
     * @internal param null|string $guard
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        try {
            $newToken = $this->guard->parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            throw new AuthTokenExpiredException();
        } catch (\Exception $e) {
            throw new AuthInvalidTokenException();
        }

        $response->headers->set('Authorization', 'Bearer ' . $newToken);

        return $response;
    }
}
