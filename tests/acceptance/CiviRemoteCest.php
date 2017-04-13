<?php


class CiviRemoteCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
    }

    public function testCiviRemoteAPI(AcceptanceTester $I)
    {
          $I->assertEquals($I->getConfig('admin_username'), $I->getConfig('admin_password'));
    }

}
