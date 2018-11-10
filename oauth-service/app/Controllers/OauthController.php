<?php
namespace App\Controllers;

class OauthController extends BaseController
{
    public $oauth;

    public function __construct(\OAuth2\Server $oauth)
    {
        $this->oauth = $oauth;
    }

    public function getToken()
    {
        $this->oauth->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
    }

    public function getProtected()
    {
        if (!$this->oauth->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
            $this->oauth->getResponse()->send();
            die;
        }
        return 'thats secret';
    }
}
