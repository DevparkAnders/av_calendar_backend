<?php

namespace Tests\Functional\App\Http\Controllers;

use App\Helpers\ErrorCode;
use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function testIndex_whenAdmin()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        $this->get('/users')
            ->seeStatusCode(200)
            ->isJson();

        // get all users from database
        $users = User::orderBy('id')->get();

        // make sure in response we have all users
        $json = $this->decodeResponseJson();
        $responseUsers = $json['data'];
        $this->assertEquals($users->count(), count($responseUsers));
        $this->assertEquals($this->formatUsers($users), $responseUsers);
    }

    public function testIndex_whenDeveloperWithoutProjects()
    {
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        $this->get('/users')
            ->seeStatusCode(200)
            ->isJson();

        // get expected users from database
        $users = User::where('id', $this->user->id)->orderBy('id')->get();

        // make sure in response we have only current user
        $json = $this->decodeResponseJson();
        $responseUsers = $json['data'];
        $this->assertEquals(1, count($responseUsers));
        $this->assertEquals($this->formatUsers($users), $responseUsers);
    }

    public function testIndex_whenDeveloperWithProjects()
    {
        $this->createUser()->setRole(RoleType::DEVELOPER);

        $newUsers = factory(User::class, 7)->create();

        // now we assign current user and other users into different project
        // current user we assign to project 1 and 3
        \DB::table('project_user')->insert([
                [
                    'project_id' => 1,
                    'user_id' => $this->user->id,
                ],
                [
                    'project_id' => 1,
                    'user_id' => $newUsers[0]->id,
                ],
                [
                    'project_id' => 1,
                    'user_id' => $newUsers[3]->id,
                ],
                [
                    'project_id' => 3,
                    'user_id' => $this->user->id,
                ],
                [
                    'project_id' => 3,
                    'user_id' => $newUsers[2]->id,
                ],
                [
                    'project_id' => 3,
                    'user_id' => $newUsers[4]->id,
                ],
                [
                    'project_id' => 4,
                    'user_id' => $newUsers[1]->id,
                ],
                [
                    'project_id' => 4,
                    'user_id' => $newUsers[5]->id,
                ],
                [
                    'project_id' => 4,
                    'user_id' => $newUsers[6]->id,
                ],
            ]
        );
        auth()->loginUsingId($this->user->id);

        $this->get('/users')
            ->seeStatusCode(200)
            ->isJson();

        // get expected users from database
        $users = User::whereIn('id', [
            $this->user->id,
            $newUsers[0]->id,
            $newUsers[3]->id,
            $newUsers[2]->id,
            $newUsers[4]->id,
        ])->orderBy('id')->get();

        // make sure in response we have all valid users
        $json = $this->decodeResponseJson();
        $responseUsers = $json['data'];
        $this->assertEquals($users->count(), count($responseUsers));
        $this->assertEquals($this->formatUsers($users), $responseUsers);
    }

    public function testStoreUser_whenNoData()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);
        $this->post('/users')->seeStatusCode(422)
            ->seeJsonContains(['code' => ErrorCode::VALIDATION_FAILED])
            ->seeJsonStructure([
                'fields' => [
                    'email',
                    'role_id',
                ],
            ])
            ->isJson();
    }

    public function testStoreUser_withData()
    {
        $this->expectsEvents(\App\Modules\User\Events\UserWasCreated::class);

        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        $data = factory(User::class, 1)->make()->toArray();
        $data['password'] = 'xxx22c';
        $data['password_confirmation'] = $data['password'];
        $data['send_user_notification'] = true;
        $data['url'] = 'http://example.com';

        $this->post('/users', $data)->seeStatusCode(201);

        // make sure in response we have valid user data
        $json = $this->decodeResponseJson();
        $responseUser = $json['data'];

        $this->assertEquals($data['first_name'], $responseUser['first_name']);
        $this->assertEquals($data['last_name'], $responseUser['last_name']);
        $this->assertEquals($data['email'], $responseUser['email']);
        $this->assertEquals($data['role_id'], $responseUser['role_id']);

        // db verification
        $dbUser = User::find($responseUser['id']);
        $this->assertEquals($dbUser->email, $data['email']);

        // verify whether use can log in
        auth()->logout();
        $this->assertFalse(auth()->check());

        auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $this->assertTrue(auth()->check());
    }

    public function testStoreUser_withoutPasswordAndNotification()
    {
        $this->expectsEvents(\App\Modules\User\Events\UserWasCreated::class);

        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        $data = factory(User::class, 1)->make()->toArray();
        $data['password'] = '';
        $data['password_confirmation'] = $data['password'];
        $data['send_user_notification'] = false;

        $this->post('/users', $data)->seeStatusCode(201);

        // make sure in response we have valid user data
        $json = $this->decodeResponseJson();
        $responseUser = $json['data'];

        $this->assertEquals($data['first_name'], $responseUser['first_name']);
        $this->assertEquals($data['last_name'], $responseUser['last_name']);
        $this->assertEquals($data['email'], $responseUser['email']);
        $this->assertEquals($data['role_id'], $responseUser['role_id']);

        // db verification
        $dbUser = User::find($responseUser['id']);
        $this->assertEquals($data['email'], $dbUser->email);
        $this->assertEquals($data['role_id'], $dbUser->role_id);

        $this->assertNotEquals($dbUser->password, '');
    }

    public function testStoreUser_whenNotLogged()
    {
        $this->doesntExpectEvents(\App\Modules\User\Events\UserWasCreated::class);

        $this->post('/users', [])->seeStatusCode(401);
    }

    public function testStoreUser_whenNotLoggedWithoutPassword()
    {
        $this->withoutMiddleware();
        $data = factory(User::class, 1)->make()->toArray();
        $data['password'] = '';
        $data['first_name'] = '';
        $data['last_name'] = '';
        $data['role_id'] = 1;
        $data['password_confirmation'] = $data['password'];
        $data['send_user_notification'] = true;

        $this->post('/users', $data)->seeStatusCode(422)
            ->seeJsonContains(['code' => ErrorCode::VALIDATION_FAILED])
            ->seeJsonStructure([
                'fields' => [
                    'password',
                    'first_name',
                    'last_name'
                ],
            ])
            ->isJson();
    }

    public function testStoreUser_whenLoggedAsNonAdmin()
    {
        $this->withoutMiddleware();
        $this->expectsEvents(\App\Modules\User\Events\UserWasCreated::class);
        $this->createUser()->setRole(RoleType::CLIENT);

        $this->verifyUserCreationForNonAdminUser();
    }

    protected function verifyUserCreationForNonAdminUser()
    {
        $data = factory(User::class, 1)->make()->toArray();
        $data['password'] = 'abc34aa';
        $data['role_id'] = 1;
        $data['password_confirmation'] = $data['password'];
        $data['send_user_notification'] = false;

        $this->post('/users', $data)->seeStatusCode(201);

        // make sure in response we have valid user data
        $json = $this->decodeResponseJson();
        $responseUser = $json['data'];

        $expectedRoleId = Role::where('name', RoleType::default())
            ->first()->id;

        $this->assertEquals($data['first_name'], $responseUser['first_name']);
        $this->assertEquals($data['last_name'], $responseUser['last_name']);
        $this->assertEquals($data['email'], $responseUser['email']);
        $this->assertEquals($expectedRoleId, $responseUser['role_id']);

        // db verification
        $dbUser = User::find($responseUser['id']);
        $this->assertEquals($data['email'], $dbUser->email);
        $this->assertEquals($expectedRoleId, $dbUser->role_id);

        $this->assertNotEquals($dbUser->password, '');        
    }
}
