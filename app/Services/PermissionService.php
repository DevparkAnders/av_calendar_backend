<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Logging\Log;

class PermissionService
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Log
     */
    protected $log;

    /**
     * PermissionService constructor.
     *
     * @param Log $log
     * @param Config $config
     */
    public function __construct(Log $log, Config $config)
    {
        $this->config = $config;
        $this->log = $log;
    }

    /**
     * Verify if user has all given permissions
     *
     * @param User $user
     * @param string|array $permission
     *
     * @return bool
     */
    public function can(User $user, $permission)
    {
        return $this->canAll($user, (array)$permission);
    }

    /**
     * Verify if user has all given permissions
     *
     * @param User $user
     * @param array $permissions
     *
     * @return bool
     */
    public function canAll(User $user, array $permissions)
    {
        $userPermissions = $this->getUserPermissions($user);

        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify if user has any given permission
     *
     * @param User $user
     * @param array $permissions
     *
     * @return bool
     */
    public function canAny(User $user, array $permissions)
    {
        $userPermissions = $this->getUserPermissions($user);

        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user permissions
     *
     * @param User $user
     *
     * @return array
     */
    protected function getUserPermissions(User $user)
    {
        $config = $this->getPermissionsConfig();

        $roleName = $user->role->name;

        if (!isset($config['roles'][$roleName])) {
            $this->log->critical("There are no roles assigned to role {$roleName}");

            return [];
        }

        $permissions = $config['roles'][$roleName];

        if (count($permissions) == 1 && $permissions[0] == '*') {
            return $config['all'];
        }

        return $permissions;
    }

    /**
     * Get permissions config
     *
     * @return array
     */
    private function getPermissionsConfig()
    {
        return $this->config->get('permissions');
    }
}
