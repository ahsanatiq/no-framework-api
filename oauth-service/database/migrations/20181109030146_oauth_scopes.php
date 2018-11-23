<?php
namespace database;

use Phinx\Migration\AbstractMigration;

class OauthScopes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('oauth_scopes');
        $table
        ->addColumn('scope', 'string', ['null'=>false, 'limit' => 80])
        ->addColumn('is_default', 'boolean', ['null'=>true])
        ->create();
    }
}
