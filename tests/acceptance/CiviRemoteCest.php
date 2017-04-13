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

    /**
     * API only test.
     *
     * @group API
     */
    public function basicApiException(AcceptanceTester $I) {
/*
      $I->expectException(\CiviCRM_Exception::class, function () {
        $outlandish_id = -999;
        $contact = $I->civicrm_api3('Contact', 'getSingle', [
          'id' => $outlandish_id,
          'sequential' => 1
        ]);
      });
*/
    }

    public function testCiviRemoteAPI(AcceptanceTester $I)
    {
      // Get tat Civi config and API. (API class shoul come from composer.)
      // $I->assertEquals($I->getConfig('path_civicrm_settings'), $I->getConfig('path_civicrm_api'));
      require_once $I->getConfig('path_civicrm_settings');
      require_once $I->getConfig('path_civicrm_api');

      // Ensure we have some contacts in our DB.
      $civicrm_api = $I->getCivicrmApiClass();
      $I->assertEquals('foo', gettype($civicrm_api));
      $result = $civicrm_api->get('Contact', 'get', []);

      $I->assertEquals($result, $I->getConfig('admin_password'));
    }

}
