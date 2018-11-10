<?php


use Phinx\Migration\AbstractMigration;

class OauthClientsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_clients');
        $table
        ->addColumn('name', 'string', ['limit' => 100])
        ->addColumn('client_id', 'string', ['null'=>true, 'limit' => 80])
        ->addColumn('client_secret', 'string', ['null'=>true, 'limit' => 80])
        ->addColumn('redirect_uri', 'string', ['null'=>true, 'limit' => 2000])
        ->addColumn('grant_types', 'string', ['null'=>true, 'limit' => 80])
        ->addColumn('scope', 'string', ['null'=>true, 'limit' => 4000])
        ->addColumn('user_id', 'string', ['null'=>true, 'limit' => 80])
        ->create();
    }
}
