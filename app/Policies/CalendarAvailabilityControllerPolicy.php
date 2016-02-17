<?php

namespace App\Policies;

use App\Models\User;

class CalendarAvailabilityControllerPolicy extends BasePolicy
{
    protected $group = 'calendar';

    public function show(User $user, User $displayedUser, $date)
    {
        // user can display calendar availability only for allowed users
        return (bool)in_array($displayedUser->id,
            User::allowed()->pluck('id')->all());
    }
}
