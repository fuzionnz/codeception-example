<?php

/**
 * A functional test to demonstrate using the CiviRemote helper module.
 */
class CiviRemoteCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * API only test.
     *
     * @group api
     */
    public function basicApiException(AcceptanceTester $I) {
        $I->expectException(\PHPUnit_Framework_Exception::class, function () {
            $outlandish_id = -999;
            $contact = $I->CiviRemote([
                'entity' => 'Contact',
                'action' => 'getSingle',
                'contact_type' => 'Individual',
                'id' => $outlandish_id,
                'sequential' => 1
            ]);
        });
    }

  /**
   * @param \AcceptanceTester $I
   *
   * @group api
   */
  public function testCiviRemoteAPI(AcceptanceTester $I)
  {
    // Ensure we have some contacts in our DB.
    $result = $I->CiviRemote([
        'entity' => 'contact',
        'action' => 'get',
        'contact_type' => 'Individual',
        'options' => [
            'limit' => 1,
            'sequential' => 1,
        ],
    ]);
    $I->assertEquals(0, $result['is_error'], 'Result is not an error.');

    // Why is it not sequential?
    $contact = reset($result['values']);
    $I->assertArrayHasKey('display_name', $contact);
  }

}
