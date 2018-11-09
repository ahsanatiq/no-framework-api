<?php


use Phinx\Migration\AbstractMigration;

class OauthClientsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_clients');
        $table
        ->addColumn('name', 'string', ['null'=>false, 'limit' => 100])
        ->addColumn('client_id', 'string', ['limit' => 80])
        ->addColumn('client_secret', 'string', ['limit' => 80])
        ->addColumn('redirect_uri', 'string', ['limit' => 2000])
        ->addColumn('grant_types', 'string', ['limit' => 80])
        ->addColumn('scope', 'string', ['limit' => 4000])
        ->addColumn('user_id', 'string', ['limit' => 80])
        ->create();
    }
}
