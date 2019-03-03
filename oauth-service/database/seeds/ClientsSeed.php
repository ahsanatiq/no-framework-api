<?php
namespace database;

use Phinx\Seed\AbstractSeed;

class ClientsSeed extends AbstractSeed
{

    public function run()
    {
        $this->insert('oauth_clients', [
            'name' => 'Test Client',
            'client_id' => 'testclient',
            'client_secret' => 'testpass',
            'user_id' => '1',
            'redirect_uri' => 'http://oauth-service/',
        ]);
    }
}
