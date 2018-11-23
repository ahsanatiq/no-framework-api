<?php
namespace database;

use Phinx\Migration\AbstractMigration;

class OauthUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_users');
        $table
        ->addColumn('user_id', 'string', ['limit' => 80])
        ->addColumn('username', 'string', ['limit' => 80])
        ->addColumn('password', 'string', ['limit' => 80])
        ->addColumn('first_name', 'string', ['null'=>true, 'limit' => 80])
        ->addColumn('last_name', 'string', ['null'=>true, 'limit' => 80])
        ->addColumn('email', 'string', ['null'=>true, 'limit' => 80])
        ->addColumn('email_verified', 'boolean', ['null'=>true])
        ->addColumn('scope', 'string', ['null'=>true, 'limit' => 4000])
        ->create();
    }
}
