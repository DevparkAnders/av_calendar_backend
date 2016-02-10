<?php

namespace App\Modules\CalendarAvailability\Services;

use App\Modules\CalendarAvailability\Contracts\CalendarAvailabilityFormatter as CalendarAvailabilityFormatterContract;
use Illuminate\Database\Eloquent\Collection;

class CalendarAvailabilityFormatter implements CalendarAvailabilityFormatterContract
{

    /**
     * Format calendar availabilities
     *
     * @param Collection $availabilities
     *
     * @return array
     */
    public function formatAvailabilities(Collection $availabilities)
    {
        $output = [];

        $data = $availabilities->groupBy('day');

        /** @var  Collection $item */
        foreach ($data as $date => $item) {
            $items = $item->groupBy('user_id');
            $av = [];
            foreach ($items as $userId => $availabilities) {
                $av[] = [
                    'id' => $userId,
                    'availabilities' => $availabilities,
                ];
            }

            $output[] = [
                'date' => $date,
                'users' => $av,
            ];
        }

        return $output;
    }

    /**
     * Format dates
     *
     * @param array $dates
     *
     * @return array
     */
    public function formatDates(array $dates)
    {
        return array_map(function ($date) {
            return [
                'date' => $date,
            ];
        }, $dates);
    }
}
