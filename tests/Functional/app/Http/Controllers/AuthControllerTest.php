<?php

namespace Tests\Functional\App\Http\Controllers;

use App\Helpers\ErrorCode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use JWTAuth;

class AuthControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function testLogin_withoutData()
    {
        $this->createUser();
        $this->post('/auth')->seeStatusCode(422)
            ->seeJsonContains(['code' => ErrorCode::VALIDATION_FAILED])
            ->seeJsonStructure([
                'fields' => [
                    'email',
                    'password',
                ],
            ])->isJson();
    }

    public function testLogin_withMissingPassowrd()
    {
        $this->createUser();
        $this->post('/auth', [
            'email' => $this->userPassword,
        ])->seeStatusCode(422)
            ->seeJsonContains(['code' => ErrorCode::VALIDATION_FAILED])
            ->seeJsonStructure([
                'fields' => [
                    'password',
                ],
            ])->isJson();
    }

    public function testLogin_withInvalidPassword()
    {
        $this->createUser();
        $data = [
            'email' => $this->userEmail,
            'password' => $this->userPassword . 'test',
        ];

        $this->post('/auth', $data)
            ->seeStatusCode(401)
            ->seeJsonContains(['code' => ErrorCode::AUTH_INVALID_LOGIN_DATA])
            ->isJson();
    }

    public function testLogin_withValidPassword()
    {
        $this->createUser();
        $data = [
            'email' => $this->userEmail,
            'password' => $this->userPassword,
        ];

        $this->post('/auth', $data)
            ->seeStatusCode(201)
            ->seeJsonStructure(['data' => ['token']])
            ->isJson();

        // get token and verify if it's valid
        $json = $this->decodeResponseJson();
        $token = $json['data']['token'];
        $this->assertEquals($this->user->id, JWTAuth::authenticate($token)->id);

        $this->assertTrue(auth()->check());
    }

    public function testLogin_withValidPasswordWhenUserDeleted()
    {
        $this->createUser(1);
        $data = [
            'email' => $this->userEmail,
            'password' => $this->userPassword,
        ];

        $this->post('/auth', $data)
            ->seeStatusCode(401)
            ->seeJsonContains(['code' => ErrorCode::AUTH_INVALID_LOGIN_DATA])
            ->isJson();

        $this->assertFalse(auth()->check());
    }

    public function testLogout_whenNotLoggedIn()
    {
        $this->createUser();
        $this->delete('/auth')
            ->seeStatusCode(401)
            ->seeJsonContains(['code' => ErrorCode::AUTH_INVALID_TOKEN])
            ->isJson();
    }

    public function testLogout_whenLoggedIn()
    {
        $this->createUser();
        $token = JWTAuth::fromUser($this->user);

        $this->delete('/auth', [], ['Authorization' => 'Bearer ' . $token])
            ->seeStatusCode(204)
            ->isJson();
    }
}
