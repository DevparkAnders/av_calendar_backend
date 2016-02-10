<?php

namespace App\Modules\CalendarAvailability\Contracts;

interface CalendarAvailability
{
    /**
     * Find calendar availability for selected period of time
     *
     * @param string $dateFrom
     * @param int $limit
     *
     * @return array
     */
    public function find($dateFrom, $limit);
}
