<?php


use Phinx\Migration\AbstractMigration;

class OauthRefreshTokens extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_refresh_tokens');
        $table
        ->addColumn('refresh_token', 'string', ['null'=>false, 'limit' => 40])
        ->addColumn('client_id', 'string', ['null'=>false, 'limit' => 80])
        ->addColumn('user_id', 'string', ['limit' => 80])
        ->addColumn('expires', 'timestamp', ['null'=>false])
        ->addColumn('scope', 'string', ['limit' => 4000])
        ->create();
    }
}
