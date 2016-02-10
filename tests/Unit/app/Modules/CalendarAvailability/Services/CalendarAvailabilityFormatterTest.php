<?php

namespace Tests\Unit\app\Modules\CalendarAvailability\Services;

use App\Models\UserAvailability;
use App\Modules\CalendarAvailability\Services\CalendarAvailabilityFormatter;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalendarAvailabilityFormatterTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @var CalendarAvailabilityFormatter
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(CalendarAvailabilityFormatter::class);
    }

    public function testFormatDays_whenNoDates()
    {
        $this->assertEquals([], $this->service->formatDates([]));
    }

    public function testFormatDays_whenDates()
    {
        $this->assertEquals([
            [
                'date' => '2015-02-12',
            ],
            [
                'date' => '2015-02-13',
            ],
        ], $this->service->formatDates(['2015-02-12', '2015-02-13']));
    }

    public function testformatAvailabilities()
    {
        $availabilities = $this->createAvailabilities();
        $av = UserAvailability::orderBy('user_id', 'ASC')
            ->orderBy('day', 'ASC')
            ->orderBy('time_start', 'ASC')->get();

        $this->assertEquals($this->getExpectedResult($availabilities),
            $this->service->formatAvailabilities($av));
    }

    protected function getExpectedResult(array $av)
    {
        return [
            [
                'date' => '2016-02-10',
                'users' => [
                    [
                        'id' => $av[0]['user_id'],
                        'availabilities' =>
                            UserAvailability::where('id', $av[0]['id'])->get()
                        ,
                    ],
                    [
                        'id' => $av[1]['user_id'],
                        'availabilities' => UserAvailability::where('id',
                            $av[1]['id'])->get(),
                    ],
                    [
                        'id' => $av[3]['user_id'],
                        'availabilities' => UserAvailability::where('id',
                            $av[3]['id'])->get(),
                    ],
                    [
                        'id' => $av[2]['user_id'],
                        'availabilities' =>
                            UserAvailability::whereIn('id',
                                [$av[4]['id'], $av[2]['id']])
                                ->orderBy('id', 'DESC')->get(),
                    ],
                ],
            ],
            [
                'date' => '2016-02-11',
                'users' => [
                    [
                        'id' => $av[5]['user_id'],
                        'availabilities' =>
                            UserAvailability::where('id', $av[5]['id'])->get()
                        ,
                    ],

                    [
                        'id' => $av[7]['user_id'],
                        'availabilities' =>
                            UserAvailability::whereIn('id',
                                [$av[7]['id'], $av[8]['id']])
                                ->orderBy('id', 'DESC')->get(),
                    ],
                    [
                        'id' => $av[6]['user_id'],
                        'availabilities' => UserAvailability::where('id',
                            $av[6]['id'])->get(),
                    ],
                ],
            ],
        ];
    }

    protected function formatAvailability(array $av)
    {
        unset($av['user_id']);
        unset($av['day']);
        $av['available'] = (bool)$av['available'];

        return $av;
    }

    protected function createAvailabilities()
    {
        $day = '2016-02-10';
        $tomorrow = '2016-02-11';

        $availabilities = [
            [
                'time_start' => '12:00:00',
                'time_stop' => '13:00:30',
                'available' => 1,
                'description' => 'Sample description own',
                'user_id' => 1,
                'day' => $day,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => 2,
                'day' => $day,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => 4,
                'day' => $day,
            ],
            [
                'time_start' => '16:00:00',
                'time_stop' => '18:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => 3,
                'day' => $day,
            ],
            [
                'time_start' => '14:00:00',
                'time_stop' => '15:00:00',
                'available' => 0,
                'description' => 'Sample description test',
                'user_id' => 4,
                'day' => $day,
            ],

            [
                'time_start' => '12:00:00',
                'time_stop' => '13:00:30',
                'available' => 1,
                'description' => 'Sample description own',
                'user_id' => 1,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '15:00:00',
                'time_stop' => '16:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => 4,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '16:00:00',
                'time_stop' => '18:00:00',
                'available' => 1,
                'description' => 'Sample description test',
                'user_id' => 2,
                'day' => $tomorrow,
            ],
            [
                'time_start' => '14:00:00',
                'time_stop' => '15:00:00',
                'available' => 0,
                'description' => 'Sample description test',
                'user_id' => 2,
                'day' => $tomorrow,
            ],
        ];

        foreach ($availabilities as $key => $av) {
            $avO = UserAvailability::forceCreate($av);
            $availabilities[$key]['id'] = $avO->id;
        }

        return $availabilities;
    }
}
