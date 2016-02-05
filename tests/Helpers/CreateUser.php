<?php

namespace Tests\Helpers;

use App\Models\Role;
use App\Models\User;

trait CreateUser
{
    /**
     * Testing user e-mail
     *
     * @var string
     */
    protected $userEmail;

    /**
     * Testing user password
     *
     * @var string
     */
    protected $userPassword;

    /**
     * User
     * 
     * @var User|null
     */
    protected $user;

    /**
     * Creates user for tests
     *
     * @param int $deleted
     *
     * @return $this
     */
    protected function createUser($deleted = 0)
    {
        $this->userEmail = 'useremail@example.com';
        $this->userPassword = 'testpassword';

        $this->user = factory(User::class, 1)->create([
            'email' => $this->userEmail,
            'password' => $this->userPassword,
            'deleted' => $deleted,
        ]);

        return $this;
    }

    /**
     * Sets user given role
     *
     * @param string $roleType
     *
     * @return $this
     */
    protected function setRole($roleType)
    {
        $this->user->role_id = Role::where('name', $roleType)->first()->id;
        $this->user->save();

        return $this;
    }
}
