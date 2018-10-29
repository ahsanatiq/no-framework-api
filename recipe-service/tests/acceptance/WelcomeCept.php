<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('perform test on root endpoint and expect 404 Not Found');
$I->sendGET('');
$I->seeResponseCodeIs(404);
// $I->see('Hello');

