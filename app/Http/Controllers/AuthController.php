<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLogin;
use App\Services\AuthService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    protected $service;

    /**
     * AuthController constructor.
     *
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Log in user
     *
     * @param AuthLogin $request
     *
     * @return Response
     */
    public function login(AuthLogin $request)
    {
        list($user, $token) = $this->service->login($request);

        return response()->api(['item' => $user], 200,
            ['Authorization' => 'Bearer ' . $token]
        );
    }

    /**
     * Log out user
     *
     * @return Response
     */
    public function logout()
    {
        $this->service->logout();

        return response()->api([], 204);
    }
}
