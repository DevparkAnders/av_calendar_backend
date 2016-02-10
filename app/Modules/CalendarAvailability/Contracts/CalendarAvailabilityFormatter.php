<?php

namespace App\Modules\CalendarAvailability\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CalendarAvailabilityFormatter
{
    /**
     * Format calendar availabilities
     *
     * @param Collection $availabilities
     *
     * @return array
     */
    public function formatAvailabilities(Collection $availabilities);

    /**
     * Format dates
     *
     * @param array $dates
     *
     * @return array
     */
    public function formatDates(array $dates);
}
