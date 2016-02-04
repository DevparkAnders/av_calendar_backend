<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

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
     * User can be assigned to multiple projects
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * User can declare multiple availabilities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availabilities()
    {
        return $this->hasMany(UserAvailability::class);
    }

    /**
     * Whether user is deleted or not
     *
     * @return bool
     */
    public function isDeleted()
    {
        return (bool)$this->deleted;
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
