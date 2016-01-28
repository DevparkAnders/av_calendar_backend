<?php

namespace App\Services;

use App\Exceptions\AuthCreateTokenException;
use App\Exceptions\AuthLoginException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthService
{
    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var Guard
     */
    protected $guard;

    /**
     * AuthService constructor.
     *
     * @param JWTAuth $auth
     * @param Guard $guard
     */
    public function __construct(JWTAuth $auth, Guard $guard)
    {
        $this->auth = $auth;
        $this->guard = $guard;
    }

    /**
     * Log in user
     *
     * @param Request $request
     *
     * @return array
     * @throws AuthCreateTokenException
     * @throws AuthLoginException
     */
    public function login(Request $request)
    {
        // we allow to log in only users that are not deleted
        $credentials = array_merge(
            $request->only('email', 'password'),
            ['deleted' => 0]);

        // invalid user
        if (!$this->guard->attempt($credentials)) {
            throw new AuthLoginException();
        }

        // get user
        $user = $this->guard->user();

        // create user token
        try {
            $token = $this->auth->fromUser($user);
        } catch (JWTException $e) {
            throw new AuthCreateTokenException();
        }

        return [$user, $token];
    }

    /**
     * Log out user
     */
    public function logout()
    {
        $this->auth->invalidate();
    }
}
