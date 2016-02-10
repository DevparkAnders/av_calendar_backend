<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User as U;

class User extends TransformerAbstract
{
    /**
     * Transform User object into array
     *
     * @param U $user
     *
     * @return array
     */
    public function transform(U $user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role_id' => $user->role_id,
            'avatar' => $user->avatar,
            'deleted' => (bool) $user->deleted,
        ];
    }
}
