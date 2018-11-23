<?php
namespace database;

use Phinx\Migration\AbstractMigration;

class OauthJwt extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_jwt');
        $table
        ->addColumn('client_id', 'string', ['null'=>false, 'limit' => 80])
        ->addColumn('subject', 'string', ['null'=>true, 'limit' => 80])
        ->addColumn('public_key', 'string', ['null'=>false, 'limit' => 2000])
        ->create();
    }
}
