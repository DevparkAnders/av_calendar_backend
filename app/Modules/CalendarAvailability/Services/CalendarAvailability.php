<?php

namespace App\Modules\CalendarAvailability\Services;

use App\Models\User;
use App\Models\UserAvailability;
use Carbon\Carbon;
use App\Modules\CalendarAvailability\Contracts\CalendarAvailability as CalendarAvailabilityContract;

class CalendarAvailability implements CalendarAvailabilityContract
{
    /**
     * @var User
     */
    protected $user;

    /**
     * CalendarAvailability constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Carbon $startDate, Carbon $endDate)
    {
        return get_class($this->user)::active()->allowed()
            ->withAvailabilities($startDate, $endDate)
            ->orderBy('id', 'ASC')->get();
    }
}
