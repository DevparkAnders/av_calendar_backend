<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Contracts\Logging\Log;

class BasePolicy
{
    /**
     * @var PermissionService
     */
    protected $permService;

    /**
     * @var string
     */
    protected $group = null;

    /**
     * @var Log
     */
    protected $log;

    /**
     * BasePolicy constructor.
     *
     * @param Log $log
     * @param PermissionService $permService
     */
    public function __construct(Log $log, PermissionService $permService)
    {
        $this->permService = $permService;
        $this->log = $log;
    }

    /**
     * Base authorization verification method. In case non-null value is
     * returned this will indicate whether user has (or not) access for given
     * resource
     *
     * @param User $user
     * @param $ability
     *
     * @return bool|null
     * @throws \Exception
     */
    public function before(User $user, $ability)
    {
        // for admin we will always allow everything no matter what specific
        // permissions are defined later
        if ($user->isAdmin()) {
            return true;
        }

        // we need to have group name defined in policy
        if ($this->group === null) {
            $this->log->error('You need to set group property in ' . __CLASS__);
            throw new \Exception('No group policy defined');
        }

        // verify if user has permission for this group and this ability
        $can = $this->permService->can($user, $this->group . '.' . $ability);

        // if user has no permission for this action, we don't need to do
        // anything more - user won't be able do run this action
        if (!$can) {
            return false;
        }

        // if he has and no custom rule defined for this ability, we assume
        // that user has permission for this action
        if (!$this->hasCustomAbilityRule($ability)) {
            return true;
        }

        // otherwise if user has this permission but custom rule is defined
        // we will go into this custom rule to verify it in details
        return null;
    }

    /**
     * Verifies if there are any custom rules defined for given ability
     *
     * @param string $ability
     *
     * @return bool
     */
    protected function hasCustomAbilityRule($ability)
    {
        return method_exists($this, $ability);
    }
}
