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
     * @group api
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

  /**
   * @param \AcceptanceTester $I
   *
   * @group api
   */
  public function testCiviRemoteAPI(AcceptanceTester $I)
  {
    $api_creds = [
      'api_key' => $I->getConfig('api_key'),
      'site_key' => $I->getConfig('site_key'),
      'url' => $I->getConfig('url'),
    ];
    // Ensure we have some contacts in our DB.
    $civicrm_api = $I->getCiviApi();
    $result = $civicrm_api->Contact->Get([
      'contact_type' => 'Individual',
      'options' => [
        'limit' => 1,
        'sequential' => 1,
      ],
    ]);
    $I->assertEquals(true, $result, 'Successful contact.get.');
    foreach ($civicrm_api->values as $contact) {
      $I->assertInternalType('object', $contact);
    }
  }

}
