<?php

namespace Helpers;

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
     * @var
     */
    protected $userPassword;

    /**
     * Creates user for tests
     *
     * @param int $deleted
     */
    protected function createUser($deleted = 0)
    {
        $this->userEmail = 'useremail@example.com';
        $this->userPassword = 'testpassword';

        $this->user = factory(User::class, 1)->create([
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPassword),
            'deleted' => $deleted,
        ]);
    }
}
