<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthAlreadyLoggedException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JwtAuth;

class VerifyIfAuthenticated
{
    /**
     * @var JwtAuth
     */
    protected $guard;

    /**
     * VerifyIfAuthenticated constructor.
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
     * @throws AuthAlreadyLoggedException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = null;
        try {
            $user = $this->guard->parseToken()->authenticate();
        } catch (\Exception $e) {
            // we don't care about exceptions in this place
        }

        // if user is not deleted it means they are already logged
        if ($user && !$user->isDeleted()) {
            throw new AuthAlreadyLoggedException();
        }

        return $next($request);
    }
}
