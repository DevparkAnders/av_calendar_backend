<?php

namespace App\Modules\CalendarAvailability\Services;

use App\Models\User;
use App\Models\UserAvailability;
use Carbon\Carbon;

class CalendarAvailability
{
    /**
     * @var User
     */
    protected $user;
    
    /**
     * @var UserAvailability
     */
    protected $userAvailability;

    /**
     * CalendarAvailability constructor.
     *
     * @param User $user
     * @param UserAvailability $userAvailability
     */
    public function __construct(User $user, UserAvailability $userAvailability)
    {
        $this->user = $user;
        $this->userAvailability = $userAvailability;
    }

    /**
     * Find calendar availability for selected period of time
     *
     * @param string $dateFrom
     * @param int $limit
     *
     * @return array
     */
    public function find($dateFrom, $limit)
    {
        // get allowed users
        $users = get_class($this->user)::active()->allowed()
            ->orderBy('id', 'ASC')->get();

        // get dates range
        $dates = $this->getDatesRange($dateFrom, $limit);

        // get calendar availability for users and dates
        $availabilities = get_class($this->userAvailability)
            ::getForObjectsAndDays($users->pluck('id')->all(), $dates);

        return [$users, $dates, $availabilities];
    }

    /**
     * Get dates range
     *
     * @param string $dateFrom
     * @param int $limit
     *
     * @return array
     */
    protected function getDatesRange($dateFrom, $limit)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $dateFrom);
        $endDate = with(clone $startDate)->addDays($limit - 1);

        $dates = [];
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay(1)) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
}
