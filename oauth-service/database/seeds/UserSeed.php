<?php


use Phinx\Seed\AbstractSeed;

class UserSeed extends AbstractSeed
{

    public function run()
    {
        $this->insert('oauth_users', [
            'username' => 'testuser',
            'password' => sha1('testpass'),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@acme.com',
            'email_verified' => false,
            'scope' => '',
        ]);
    }
}
