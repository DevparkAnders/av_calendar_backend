<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthInvalidTokenException;
use App\Exceptions\AuthTokenExpiredException;
use App\Exceptions\AuthUserNotFoundException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JwtAuth;

class Authenticate
{
    /**
     * @var JwtAuth
     */
    protected $guard;

    /**
     * Authenticate constructor.
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
     * @param  string|null $guard
     *
     * @return mixed
     * @throws AuthInvalidTokenException
     * @throws AuthTokenExpiredException
     * @throws AuthUserNotFoundException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            $user = $this->guard->parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            throw new AuthTokenExpiredException();
        } catch (\Exception $e) {
            throw new AuthInvalidTokenException();
        }
        
        // we allow authenticate only users that are not deleted
        if (!$user || $user->isDeleted()) {
            throw new AuthUserNotFoundException();
        }

        return $next($request);
    }
}
