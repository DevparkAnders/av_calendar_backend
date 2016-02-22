<?php

namespace Tests\Functional\app\Modules\CalendarAvailability\Http\Controllers;

use App\Helpers\ErrorCode;
use App\Models\RoleType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalendarAvailabilityControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function testStore_withInvalidData()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        $newUser = factory(User::class, 1)->create(['deleted' => 0]);

        $this->post('/users/' . $newUser->id . '/availabilities/' .
            Carbon::now()->format('Y-m-d'), [
            'availabilities' => [
                ['time_start' => 'test', 'available' => true, ],
                ['time_start' => '08:23:23', 'time_stop' => 'test'],
            ],
        ]);

        $this->verifyValidationResponse([
            'availabilities.0.time_start',
            'availabilities.1.time_stop',
            'availabilities.1.available',
        ], [
            'availabilities.0.available',
            'availabilities.0.user',
            'availabilities.0.day',
            'availabilities.0.time_stop',
            'availabilities.1.time_start',
            'availabilities.0.user',
            'availabilities.0.day',
        ]);
    }

    public function testStore_withValidDataWhenAdmin()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        $newUsers = factory(User::class, 2)->create(['deleted' => 0]);
        $today = Carbon::now();
        $tomorrow = with(clone $today)->addDay(1);

        // create sample availabilities for users
        \DB::table('user_availability')->insert([
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[0]->id,
                'day' => $today->format('Y-m-d'),
            ],
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[0]->id,
                'day' => $tomorrow->format('Y-m-d'),
            ],
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[1]->id,
                'day' => $today->format('Y-m-d'),
            ],
        ]);

        // verify number of results in database
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $newUsers[0]->id)
            ->where('day', $today->format('Y-m-d'))->count());
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $newUsers[0]->id)
            ->where('day', $tomorrow->format('Y-m-d'))->count());
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $newUsers[1]->id)
            ->where('day', $today->format('Y-m-d'))->count());

        $newAvailabilities = [
            [
                'time_start' => '12:00:00',
                'time_stop' => '15:00:00',
                'available' => true,
                'description' => 'Sample description',
            ],
            [
                'time_start' => '02:00:00',
                'time_stop' => '04:00:00',
                'available' => false,
                'description' => "Sorry I'm out ",
            ],
        ];

        $expectedAvailabilities =
            $this->getExpectedAvailabilities($newAvailabilities, $today);

        $this->post('/users/' . $newUsers[0]->id . '/availabilities/' .
            $today->format('Y-m-d'), [
            'availabilities' => $newAvailabilities,
        ])->seeStatusCode(201)->seeJsonContains([
            'data' => [
                $expectedAvailabilities[1],
                $expectedAvailabilities[0],
            ],
        ])->isJson();

        // make sure the order in response is appropriate
        $json = $this->decodeResponseJson()['data'];
        $this->assertEquals($expectedAvailabilities[1], $json[0]);
        $this->assertEquals($expectedAvailabilities[0], $json[1]);

        // verify number of results in database
        $this->assertEquals(2, \DB::table('user_availability')
            ->where('user_id', $newUsers[0]->id)
            ->where('day', $today->format('Y-m-d'))->count());
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $newUsers[0]->id)
            ->where('day', $tomorrow->format('Y-m-d'))->count());
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $newUsers[1]->id)
            ->where('day', $today->format('Y-m-d'))->count());

        // verify if new records are in database
        $this->seeInDatabase('user_availability',
            array_merge($expectedAvailabilities[0], [
                'user_id' => $newUsers[0]->id,
            ]));
        $this->seeInDatabase('user_availability',
            array_merge($expectedAvailabilities[1], [
                'user_id' => $newUsers[0]->id,
            ]));
    }

    public function testStore_withValidDataWhenNotAdmin()
    {
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        $newUsers = factory(User::class, 2)->create(['deleted' => 0]);
        $today = Carbon::now();
        $tomorrow = with(clone $today)->addDay(1);

        // create sample availabilities for users
        \DB::table('user_availability')->insert([
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[0]->id,
                'day' => $today->format('Y-m-d'),
            ],
        ]);

        // verify number of results in database
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $newUsers[0]->id)
            ->where('day', $today->format('Y-m-d'))->count());

        $newAvailabilities = [
            [
                'time_start' => '12:00:00',
                'time_stop' => '15:00:00',
                'available' => true,
                'description' => 'Sample description',
            ],
            [
                'time_start' => '02:00:00',
                'time_stop' => '04:00:00',
                'available' => false,
                'description' => "Sorry I'm out ",
            ],
        ];

        $this->post('/users/' . $newUsers[0]->id . '/availabilities/' .
            $today->format('Y-m-d'), [
            'availabilities' => $newAvailabilities,
        ]);

        $this->verifyValidationResponse(['user']);

        // verify number of results in database
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $newUsers[0]->id)
            ->where('day', $today->format('Y-m-d'))->count());

        // verify if new records are in database
        $this->dontSeeInDatabase('user_availability',
            array_merge($newAvailabilities[0], [
                'user_id' => $newUsers[0]->id,
                'day' => $today->format('Y-m-d'),
            ]));
        $this->dontSeeInDatabase('user_availability',
            array_merge($newAvailabilities[1], [
                'user_id' => $newUsers[0]->id,
                'day' => $today->format('Y-m-d'),
            ]));
    }

    public function testStore_withValidDataWhenNotForHimself()
    {
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        $newUsers = factory(User::class, 2)->create(['deleted' => 0]);
        $today = Carbon::now();
        $tomorrow = with(clone $today)->addDay(1);

        // create sample availabilities for users
        \DB::table('user_availability')->insert([
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $this->user->id,
                'day' => $today->format('Y-m-d'),
            ],
        ]);

        // verify number of results in database
        $this->assertEquals(1, \DB::table('user_availability')
            ->where('user_id', $this->user->id)
            ->where('day', $today->format('Y-m-d'))->count());

        $newAvailabilities = [
            [
                'time_start' => '12:00:00',
                'time_stop' => '15:00:00',
                'available' => true,
                'description' => 'Sample description',
            ],
            [
                'time_start' => '02:00:00',
                'time_stop' => '04:00:00',
                'available' => false,
                'description' => "Sorry I'm out ",
            ],
        ];

        $expectedAvailabilities =
            $this->getExpectedAvailabilities($newAvailabilities, $today);

        $this->post('/users/' . $this->user->id . '/availabilities/' .
            $today->format('Y-m-d'), [
            'availabilities' => $newAvailabilities,
        ])->seeStatusCode(201)->seeJsonContains([
            'data' => [
                $expectedAvailabilities[1],
                $expectedAvailabilities[0],
            ],
        ])->isJson();

        // make sure the order in response is appropriate
        $json = $this->decodeResponseJson()['data'];
        $this->assertEquals($expectedAvailabilities[1], $json[0]);
        $this->assertEquals($expectedAvailabilities[0], $json[1]);

        // verify number of results in database
        $this->assertEquals(2, \DB::table('user_availability')
            ->where('user_id', $this->user->id)
            ->where('day', $today->format('Y-m-d'))->count());

        // verify if new records are in database
        $this->seeInDatabase('user_availability',
            array_merge($expectedAvailabilities[0], [
                'user_id' => $this->user->id,
            ]));
        $this->seeInDatabase('user_availability',
            array_merge($expectedAvailabilities[1], [
                'user_id' => $this->user->id,
            ]));
    }

    protected function getExpectedAvailabilities(
        array $availabilities,
        Carbon $date
    ) {
        $expectedAvailabilities = [];

        foreach ($availabilities as $av) {
            $expectedAvailabilities[] =
                array_merge($av, ['day' => $date->format('Y-m-d')]);
        }

        return $expectedAvailabilities;
    }

    public function testShow_whenUserDoesNotExists()
    {
        $this->get('/users/' . 99999999 . '/availabilities/' .
            Carbon::now()->format('Y-m-d'));
        $this->verifyErrorResponse(404, ErrorCode::NOT_FOUND);
    }

    public function testShow_whenUserExistsForToday()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/' . $newUsers[0]->id . '/availabilities/' .
            $today->format('Y-m-d'))->seeStatusCode(200)->seeJsonContains([
            'data' => [
                $this->formatAvailability($availabilities[2]),
                $this->formatAvailability($availabilities[1]),
            ],
        ])->isJson();

        // make sure the order in response is appropriate
        $json = $this->decodeResponseJson()['data'];
        $this->assertEquals($this->formatAvailability($availabilities[2]),
            $json[0]);
        $this->assertEquals($this->formatAvailability($availabilities[1]),
            $json[1]);
    }

    public function testShow_whenUserExistsForTomorrow()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/' . $newUsers[0]->id . '/availabilities/' .
            $tomorrow->format('Y-m-d'))->seeStatusCode(200)->seeJsonContains([
            'data' => [
                $this->formatAvailability($availabilities[3]),
            ],
        ])->isJson();
    }

    public function testShow_whenAdmin()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/' . $newUsers[0]->id . '/availabilities/' .
            $tomorrow->format('Y-m-d'))->seeStatusCode(200);
    }

    public function testShow_whenDeveloper_forHimself()
    {
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/' . $this->user->id . '/availabilities/' .
            $tomorrow->format('Y-m-d'))->seeStatusCode(200);
    }

    public function testShow_whenDeveloper_forOtherUser()
    {
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/' . $newUsers[0]->id . '/availabilities/' .
            $tomorrow->format('Y-m-d'));

        $this->verifyErrorResponse(401, ErrorCode::NO_PERMISSION);
    }

    public function testShow_whenDeveloper_forOtherUserInSameProject()
    {
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        \DB::table('project_user')->insert([
            [
                'project_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 1,
                'user_id' => $newUsers[0]->id,
            ],
        ]);

        $this->get('/users/' . $newUsers[0]->id . '/availabilities/' .
            $tomorrow->format('Y-m-d'))->seeStatusCode(200);
    }

    public function testIndex_withoutParameters()
    {
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);
        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/availabilities?');

        $this->verifyValidationResponse(['from'], ['limit']);
    }

    public function testIndex_whenAdmin()
    {
        \DB::table('users')->delete();

        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);
        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/availabilities?from=' . $today->format('Y-m-d') .
            '&limit=4')
            ->seeStatusCode(200)->isJson();
        
        $json = $this->decodeResponseJson();

        $data = $json['data'];
        
        $this->assertEquals($today->format('Y-m-d'), $json['date_start']);
        $this->assertEquals($today->addDays(3)->format('Y-m-d'), $json['date_end']);
        

        $this->assertEquals(1 + $newUsers->count(), count($data));

        $this->assertEquals(array_merge($this->formatUser($this->user), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[0]),
                ],
            ],
        ]), $data[0]);

        $this->assertEquals(array_merge($this->formatUser($newUsers[0]), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[2]),
                        $this->formatAvailability($availabilities[1]),
                        $this->formatAvailability($availabilities[3]),
                ],
            ],
        ]), $data[1]);

        $this->assertEquals(array_merge($this->formatUser($newUsers[1]), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[5]),
                ],
            ],
        ]), $data[2]);

        $this->assertEquals(array_merge($this->formatUser($newUsers[2]), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[4]),
                ],
            ],
        ]), $data[3]);

        $this->assertEquals(array_merge($this->formatUser($newUsers[3]), [
            'availabilities' => [
                'data' => [],
            ],
        ]), $data[4]);
    }

    public function testIndex_whenDeveloperWithoutProjects()
    {
        \DB::table('users')->delete();

        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);
        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

        $this->get('/users/availabilities?from=' . $today->format('Y-m-d') .
            '&limit=4')
            ->seeStatusCode(200)->isJson();

        $data = $this->decodeResponseJson()['data'];
        
        $this->assertEquals(1, count($data));

        $this->assertEquals(array_merge($this->formatUser($this->user), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[0]),
                ],
            ],
        ]), $data[0]);
    }

    public function testIndex_whenDeveloperWithProjects()
    {
        \DB::table('users')->delete();

        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);
        list($newUsers, $today, $tomorrow, $availabilities) =
            $this->prepareGetData();

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
                    'project_id' => 3,
                    'user_id' => $this->user->id,
                ],
                [
                    'project_id' => 3,
                    'user_id' => $newUsers[2]->id,
                ],
                [
                    'project_id' => 2,
                    'user_id' => $newUsers[1]->id,
                ],
                [
                    'project_id' => 8,
                    'user_id' => $this->user->id,
                ],
                [
                    'project_id' => 8,
                    'user_id' => $newUsers[3]->id,
                ],
            ]
        );

        $this->get('/users/availabilities?from=' . $today->format('Y-m-d') .
            '&limit=4')
            ->seeStatusCode(200)->isJson();


        $data = $this->decodeResponseJson()['data'];

        $this->assertEquals(1 + 3, count($data));

        $this->assertEquals(array_merge($this->formatUser($this->user), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[0]),
                ],
            ],
        ]), $data[0]);

        $this->assertEquals(array_merge($this->formatUser($newUsers[0]), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[2]),
                    $this->formatAvailability($availabilities[1]),
                    $this->formatAvailability($availabilities[3]),
                ],
            ],
        ]), $data[1]);

        $this->assertEquals(array_merge($this->formatUser($newUsers[2]), [
            'availabilities' => [
                'data' => [
                    $this->formatAvailability($availabilities[4]),
                ],
            ],
        ]), $data[2]);

        $this->assertEquals(array_merge($this->formatUser($newUsers[3]), [
            'availabilities' => [
                'data' => [],
            ],
        ]), $data[3]);
    }

    protected function prepareGetData()
    {
        $newUsers = factory(User::class, 4)->create(['deleted' => 0]);
        $today = Carbon::now();
        $tomorrow = with(clone $today)->addDay(1);

        $availabilities = [
            [
                'time_start' => '12:00:00',
                'time_stop' => '13:00:30',
                'available' => 1,
                'description' => 'Sample description own',
                'user_id' => $this->user->id,
                'day' => $today->format('Y-m-d'),
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[0]->id,
                'day' => $today->format('Y-m-d'),
            ],
            [
                'time_start' => '13:00:00',
                'time_stop' => '14:00:00',
                'available' => 0,
                'description' => 'Sample description test 2',
                'user_id' => $newUsers[0]->id,
                'day' => $today->format('Y-m-d'),
            ],
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[0]->id,
                'day' => $tomorrow->format('Y-m-d'),
            ],
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[2]->id,
                'day' => with(clone $tomorrow)->addDay(2)->format('Y-m-d'),
            ],
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[1]->id,
                'day' => $today->format('Y-m-d'),
            ],
            [
                'time_start' => '00:00:00',
                'time_stop' => '01:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $newUsers[1]->id,
                'day' => with(clone $today)->addDays(20)->format('Y-m-d'),
            ],
        ];

        // create sample availabilities for users
        \DB::table('user_availability')->insert($availabilities);

        return [$newUsers, $today, $tomorrow, $availabilities];
    }

    protected function formatAvailability(array $av)
    {
        unset($av['user_id']);
        $av['available'] = (bool)$av['available'];

        return $av;
    }
}
