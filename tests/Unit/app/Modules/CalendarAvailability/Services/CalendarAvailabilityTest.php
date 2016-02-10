<?php

namespace Tests\Unit\app\Modules\CalendarAvailability\Services;

use App\Models\RoleType;
use App\Models\User;
use App\Models\UserAvailability;
use App\Modules\CalendarAvailability\Services\CalendarAvailability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalendarAvailabilityTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @var CalendarAvailability
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(CalendarAvailability::class);
    }

    public function testFind_verifyDatesForSingleDay()
    {
        list(, $dates, ) = $this->service->find('2016-02-10', 1);
        $this->assertEquals([
            '2016-02-10',
        ], $dates);
    }

    public function testFind_verifyDatesForMultipleDays()
    {
        list(, $dates, ) = $this->service->find('2016-02-25', 10);
        $this->assertEquals([
            '2016-02-25',
            '2016-02-26',
            '2016-02-27',
            '2016-02-28',
            '2016-02-29',
            '2016-03-01',
            '2016-03-02',
            '2016-03-03',
            '2016-03-04',
            '2016-03-05',
        ], $dates);
    }

    public function testFind_verifyUsers_forAdmin()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        $usersNotDeleted = factory(User::class, 3)->create(['deleted' => 0]);
        $usersDeleted = factory(User::class, 2)->create(['deleted' => 1]);

        list($users, ) = $this->service->find('2016-02-10', 1);
        $this->assertEquals(1 + 3, $users->count());

        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals($usersNotDeleted[0]->id, $users[1]->id);
        $this->assertEquals($usersNotDeleted[1]->id, $users[2]->id);
        $this->assertEquals($usersNotDeleted[2]->id, $users[3]->id);
    }

    public function testFind_verifyUsers_forDeveloper()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        $usersNotDeleted = factory(User::class, 3)->create(['deleted' => 0]);
        $usersDeleted = factory(User::class, 2)->create(['deleted' => 1]);

        \DB::table('project_user')->insert([
            [
                'project_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 1,
                'user_id' => $usersNotDeleted[0]->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $usersDeleted[0]->id,
            ],
        ]);

        list($users, ) = $this->service->find('2016-02-10', 1);
        $this->assertEquals(1 + 1, $users->count());

        $this->assertEquals($this->user->id, $users[0]->id);
        $this->assertEquals($usersNotDeleted[0]->id, $users[1]->id);
    }

    public function testFind_verifyAvailabilitiesForAdminSingleDay()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities) =
            $this->createAvailabilities();

        list(, , $av) = $this->service->find($day, 1);

        $this->assertEquals(4, $av->count());
        $this->assertEquals($availabilities[0]['id'], $av[0]->id);
        $this->assertEquals($availabilities[4]['id'], $av[1]->id);
        $this->assertEquals($availabilities[3]['id'], $av[2]->id);
        $this->assertEquals($availabilities[2]['id'], $av[3]->id);
    }

    public function testFind_verifyAvailabilitiesForAdminMultipleDays()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::ADMIN);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities) =
            $this->createAvailabilities();

        list(, , $av) = $this->service->find($day, 2);
        $this->assertEquals(8, $av->count());

        $this->assertEquals($availabilities[0]['id'], $av[0]->id);
        $this->assertEquals($availabilities[5]['id'], $av[1]->id);
        $this->assertEquals($availabilities[4]['id'], $av[2]->id);
        $this->assertEquals($availabilities[3]['id'], $av[3]->id);
        $this->assertEquals($availabilities[9]['id'], $av[4]->id);
        $this->assertEquals($availabilities[8]['id'], $av[5]->id);
        $this->assertEquals($availabilities[2]['id'], $av[6]->id);
        $this->assertEquals($availabilities[7]['id'], $av[7]->id);
    }

    public function testFind_verifyAvailabilitiesForDeveloperSingleDay()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities, $usersDeleted) =
            $this->createAvailabilities();

        \DB::table('project_user')->insert([
            [
                'project_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 1,
                'user_id' => $usersNotDeleted[1]->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $usersDeleted[0]->id,
            ],
        ]);

        list(, , $av) = $this->service->find($day, 1);

        $this->assertEquals(3, $av->count());
        $this->assertEquals($availabilities[0]['id'], $av[0]->id);
        $this->assertEquals($availabilities[4]['id'], $av[1]->id);
        $this->assertEquals($availabilities[3]['id'], $av[2]->id);
    }

    public function testFind_verifyAvailabilitiesForDeveloperMultipleDays()
    {
        \DB::table('users')->delete();
        $this->createUser()->setRole(RoleType::DEVELOPER);
        auth()->loginUsingId($this->user->id);

        list($day, $usersNotDeleted, $availabilities, $usersDeleted) =
            $this->createAvailabilities();

        \DB::table('project_user')->insert([
            [
                'project_id' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 1,
                'user_id' => $usersNotDeleted[1]->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $this->user->id,
            ],
            [
                'project_id' => 5,
                'user_id' => $usersDeleted[0]->id,
            ],
        ]);

        list(, , $av) = $this->service->find($day, 2);

        $this->assertEquals(6, $av->count());
        $this->assertEquals($availabilities[0]['id'], $av[0]->id);
        $this->assertEquals($availabilities[5]['id'], $av[1]->id);
        $this->assertEquals($availabilities[4]['id'], $av[2]->id);
        $this->assertEquals($availabilities[3]['id'], $av[3]->id);
        $this->assertEquals($availabilities[9]['id'], $av[4]->id);
        $this->assertEquals($availabilities[8]['id'], $av[5]->id);
    }

    protected function createAvailabilities()
    {
        $usersNotDeleted = factory(User::class, 3)->create(['deleted' => 0]);
        $usersDeleted = factory(User::class, 2)->create(['deleted' => 1]);

        $day = '2016-02-10';
        $tomorrow = '2016-02-11';

        $availabilities = [
            [
                'time_start' => '12:00:00',
                'time_stop' => '13:00:30',
                'available' => 1,
                'description' => 'Sample description own',
                'user_id' => $this->user->id,
                'day' => $day,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersDeleted[0]->id,
                'day' => $day,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[2]->id,
                'day' => $day,
            ],
            [
                'time_start' => '16:00:00',
                'time_stop' => '18:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $day,
            ],
            [
                'time_start' => '14:00:00',
                'time_stop' => '15:00:00',
                'available' => 0,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $day,
            ],

            [
                'time_start' => '12:00:00',
                'time_stop' => '13:00:30',
                'available' => 1,
                'description' => 'Sample description own',
                'user_id' => $this->user->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersDeleted[0]->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[2]->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '16:00:00',
                'time_stop' => '18:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '14:00:00',
                'time_stop' => '15:00:00',
                'available' => 0,
                'description' => 'Sample description test',
                'user_id' => $usersNotDeleted[1]->id,
                'day' => $tomorrow,
            ],
        ];

        foreach ($availabilities as $key => $av) {
            $avO = UserAvailability::forceCreate($av);
            $availabilities[$key]['id'] = $avO->id;
        }

        return [$day, $usersNotDeleted, $availabilities, $usersDeleted];
    }
}
