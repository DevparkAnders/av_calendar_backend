<?php

namespace App\Modules\CalendarAvailability\Http\Requests;

use App\Http\Requests\Request;
use App\Models\User;

class CalendarAvailabilityShow extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'user' => ['required'],
            'day' => ['required', 'date'],
        ];

        // non-admin users can display only allowed users availabilities
        if (!auth()->user()->isAdmin()) {
            $rules['user'][] =
                'in:' . implode(',', User::allowed()->pluck('id')->all());
        }
        
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $data = parent::all();
        // add extra data that should be validated
        $data['day'] = $this->route('day');
        $data['user'] = ($user = $this->route('user')) ? $user->id : null;

        return $data;
    }
}
