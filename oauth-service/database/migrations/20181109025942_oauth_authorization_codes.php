<?php


use Phinx\Migration\AbstractMigration;

class OauthAuthorizationCodes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_authorization_codes');
        $table
        ->addColumn('authorization_code', 'string', ['null'=>false, 'limit' => 40])
        ->addColumn('client_id', 'string', ['null'=>false, 'limit' => 80])
        ->addColumn('user_id', 'string', ['limit' => 80])
        ->addColumn('redirect_uri', 'string', ['limit' => 2000])
        ->addColumn('expires', 'timestamp', ['null'=>false])
        ->addColumn('scope', 'string', ['limit' => 4000])
        ->addColumn('id_token', 'string', ['limit' => 1000])
        ->create();
    }
}
