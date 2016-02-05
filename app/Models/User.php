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
        'email',
        'password',
        'first_name',
        'last_name',
        'role_id',
        'avatar',
        'deleted',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    // relationships

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

    // scopes

    /**
     * Choose only users that are allowed to be displayed for given user (or 
     * current user if none user given) 
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param User|int|null $user
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAllowed($query, $user = null)
    {
        // get user by id or use object - if not passed any, we use current
        // user
        if ($user) {
            if (!$user instanceof User) {
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

    // accessors, mutators

    /**
     * Mutator to set password
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // functions

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
