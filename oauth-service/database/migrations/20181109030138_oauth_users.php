<?php


use Phinx\Migration\AbstractMigration;

class OauthUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_users');
        $table
        ->addColumn('username', 'string', ['limit' => 80])
        ->addColumn('password', 'string', ['limit' => 80])
        ->addColumn('first_name', 'string', ['limit' => 80])
        ->addColumn('last_name', 'string', ['limit' => 80])
        ->addColumn('email', 'string', ['limit' => 80])
        ->addColumn('email_verified', 'boolean')
        ->addColumn('scope', 'string', ['limit' => 4000])
        ->create();
    }
}
