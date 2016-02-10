<?php

namespace App\Modules\User\Traits;

use App\Models\Role;
use App\Models\RoleType;

trait Roleable
{
    /**
     * User has single role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    

    /**
     * Verify if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(RoleType::ADMIN);
    }

    /**
     * Verify if user has given role
     *
     * @param string $roleType
     *
     * @return bool
     */
    public function hasRole($roleType)
    {
        return ($this->role && $this->role->name == $roleType);
    }
}
