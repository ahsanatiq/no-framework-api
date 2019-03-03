<?php
namespace database;

use Phinx\Seed\AbstractSeed;

class UserSeed extends AbstractSeed
{

    public function run()
    {
        $this->insert('oauth_users', [
            'user_id' => '1',
            'username' => 'testuser',
            'password' => sha1('testpass'),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@acme.com',
            'email_verified' => false,
            'scope' => '',
        ]);

        $this->insert('oauth_users', [
            'user_id' => '2',
            'username' => 'testuser2',
            'password' => sha1('testpass'),
            'first_name' => 'Test2',
            'last_name' => 'User2',
            'email' => 'testuser2@acme.com',
            'email_verified' => false,
            'scope' => '',
        ]);
    }
}
