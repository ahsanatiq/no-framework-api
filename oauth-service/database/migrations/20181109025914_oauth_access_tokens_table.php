<?php


use Phinx\Migration\AbstractMigration;

class OauthAccessTokensTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_access_tokens');
        $table
        ->addColumn('access_token', 'string', ['null'=>false, 'limit' => 40])
        ->addColumn('client_id', 'string', ['null'=>false, 'limit' => 80])
        ->addColumn('user_id', 'string', ['limit' => 80])
        ->addColumn('expires', 'timestamp', ['null'=>false])
        ->addColumn('scope', 'string', ['limit' => 4000])
        ->create();
    }
}
