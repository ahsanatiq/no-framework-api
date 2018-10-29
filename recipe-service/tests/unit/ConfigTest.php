<?php
class ConfigTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        app()->run();
    }

    // tests
    public function testAppConfig()
    {
        $appConfig = config()->get('app');
        $this->assertNotEmpty($appConfig);
        $this->assertArrayHasKey('env', $appConfig);
        $this->assertArrayHasKey('name', $appConfig);
        $this->assertArrayHasKey('url', $appConfig);
    }

    public function testDbConfig()
    {
        $DbConfig = config()->get('db');
        $this->assertNotEmpty($DbConfig);
        $this->assertArrayHasKey('default', $DbConfig);
        $this->assertNotEmpty($DbConfig['default']);
        $this->assertArrayHasKey('pgsql', $DbConfig);
        $this->assertNotEmpty($DbConfig['pgsql']['driver']);
        $this->assertNotEmpty($DbConfig['pgsql']['driver']);
        $this->assertNotEmpty($DbConfig['pgsql']['host']);
        $this->assertNotEmpty($DbConfig['pgsql']['database']);
        $this->assertNotEmpty($DbConfig['pgsql']['username']);
        $this->assertNotEmpty($DbConfig['pgsql']['password']);
        $this->assertNotEmpty($DbConfig['pgsql']['port']);
        $this->assertNotEmpty($DbConfig['pgsql']['charset']);
        $this->assertNotEmpty($DbConfig['pgsql']['collation']);
    }
}
