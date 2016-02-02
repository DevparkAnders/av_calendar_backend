<?php

namespace Tests\Functional\App\Http\Controllers;

use App\Helpers\ErrorCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use JWTAuth;
use Tests\Helpers\MailTrap;

class PasswordControllerTest extends \TestCase
{
    use DatabaseTransactions;
    use MailTrap;

    protected $testUrl = 'http://example.com/:token/?email=:email';

    public function setUp()
    {
        parent::setUp();
        $this->cleanEmails();
    }

    public function testSendResetEmail_withoutData()
    {
        $this->createUser();
        $this->post('/password/reset')->seeStatusCode(422)
            ->seeJsonContains(['code' => ErrorCode::VALIDATION_FAILED])
            ->seeJsonStructure([
                'fields' => [
                    'email',
                    'url',
                ],
            ])->isJson();

        $messages = $this->getEmails();
        $this->assertEquals(0, count($messages));
    }

    public function testSendResetEmail_withInvalidEmail()
    {
        $this->createUser();
        $this->post('/password/reset', [
            'email' => $this->userEmail . 'xxx',
            'url' => $this->testUrl,
        ])->seeStatusCode(404)
            ->seeJsonContains(['code' => ErrorCode::PASSWORD_NO_USER_FOUND])
            ->isJson();

        $messages = $this->getEmails();
        $this->assertEquals(0, count($messages));
    }

    public function testSendResetEmail_withValidEmail()
    {
        $this->createUser();

        $this->post('/password/reset', [
            'email' => $this->userEmail,
            'url' => $this->testUrl,
        ])->seeStatusCode(200);

        $token = \DB::table('password_resets')->first()->token;

        $messages = $this->getEmails();
        $this->assertEquals(1, count($messages));
        $message = $messages[0];
        $this->assertEquals(trans('emails.password_reset.subject'),
            $message->subject);
        $this->assertEquals(env('EMAIL_FROM_ADDRESS'), $message->from_email);
        $this->assertEquals(env('EMAIL_FROM_NAME'), $message->from_name);
        $this->assertEquals($this->userEmail, $message->to_email);
        $this->assertContains(str_replace([':email', ':token'],
            [urlencode($this->userEmail), $token], $this->testUrl),
            $message->html_body);
    }

    public function testReset_withNoData()
    {
        $this->createUser();

        $this->put('/password/reset', [
        ])->seeStatusCode(422)
            ->seeJsonContains(['code' => ErrorCode::VALIDATION_FAILED])
            ->seeJsonStructure([
                'fields' => [
                    'token',
                    'email',
                    'password',
                ],
            ])
            ->isJson();
    }

    public function testReset_withValidData()
    {
        $this->createUser();
        $token = $this->createPasswordToken();

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ])->seeStatusCode(200)->isJson();

        // make sure password was really saved and user can use it
        $this->assertFalse(auth()->check());
        auth()->attempt([
            'email' => $this->userEmail,
            'password' => $newPassword,
        ]);
        $this->assertTrue(auth()->check());
    }

    public function testReset_withExpiredToken()
    {
        $this->createUser();
        $token = $this->createPasswordToken(true);

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ])->seeStatusCode(422)
            ->seeJsonContains(['code' => ErrorCode::PASSWORD_INVALID_TOKEN])
            ->isJson();
    }

    public function testReset_withInvalidEmail()
    {
        $this->createUser();
        $token = $this->createPasswordToken();

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail . 'a',
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ])->seeStatusCode(404)
            ->seeJsonContains(['code' => ErrorCode::PASSWORD_NO_USER_FOUND])
            ->isJson();
    }

    public function testReset_withInvalidPassword()
    {
        $this->createUser();
        $token = $this->createPasswordToken();

        $newPassword = 'test00';

        $this->put('/password/reset', [
            'email' => $this->userEmail . 'a',
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => 'test00',
        ])->seeStatusCode(404)
            ->seeJsonContains(['code' => ErrorCode::PASSWORD_NO_USER_FOUND])
            ->isJson();
    }

    protected function createPasswordToken($expired = false)
    {
        $token = str_random();
        $date = Carbon::now();
        if ($expired) {
            $date->subMinutes(config('auth.passwords.users.expire') + 1);
        }

        \DB::table('password_resets')->insert([
            'email' => $this->userEmail,
            'token' => $token,
            'created_at' => $date->format('Y-m-d H:i:s'),
        ]);

        return $token;
    }
}
