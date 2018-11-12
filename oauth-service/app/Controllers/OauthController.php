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
        $response = $this->oauth->handleTokenRequest(\OAuth2\Request::createFromGlobals());
        logger()->info($response->getStatusCode() == 200 ? 'Access token granted.' : 'Access token denied.');
        $response->send();
    }

    public function getAuthorize()
    {
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();
        if (!$this->oauth->validateAuthorizeRequest($request, $response)) {
            logger()->info('Authorize code denied.');
            $response->send();
            die;
        }
        $this->oauth->handleAuthorizeRequest($request, $response, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
        logger()->info('Authorize code granted.');
        return ['success'=>true, 'code'=> $code];
    }
}
