<?php

namespace App\Modules\User\Traits;

use App\Models\Model;

trait Allowed
{
    /**
     * Choose only users that are allowed to be displayed for given user (or
     * current user if none user given)
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param Model|int|null $user
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAllowed($query, $user = null)
    {
        // get user by id or use object - if not passed any, we use current
        // user
        if ($user) {
            if (!$user instanceof Model) {
                $user = self::find($user);
            }
        } else {
            $user = auth()->user();
        }

        // user has not been found - return no results
        if (!$user) {
            return $query->where('id', 0);
        }

        // for admin we don't limit results
        if ($user->isAdmin()) {
            return $query;
        }

        // for others we will choose only users assigned to same projects
        return $query->where(function ($q) use ($user) {
            $q->where('id', $user->id)->orWhere(function ($q) use ($user) {
                $q->whereIn($this->getTable() . '.id',
                    function ($q) use ($user) {
                        $q->select('user_id')->from('project_user')
                            ->whereIn('project_id', function ($q) use ($user) {
                                $q->select('project_id')->from('project_user')
                                    ->where('user_id', $user->id);
                            });
                    });
            });
        });
    }
}
