<?php

/**
 * A functional test to demonstrate using the CiviRemote helper module.
 */
class CiviRemoteApiCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \AcceptanceTester $I
     *
     * @group api
     * @group civiremoteapi
     */
    public function testCiviRemoteAPI(AcceptanceTester $I)
    {
        $config = \Codeception\Configuration::config();
        $civiRemoteApi = new \CiviRemoteApi($config['modules']['config']['CiviRemoteApi']);

        $params = [
          'entity' => 'contact',
          'action' => 'get',
          'contact_type' => 'Individual',
          'options' => [
            // 'limit' => 1,
            'sequential' => 1,
          ],
        ];

        $contacts = $civiRemoteApi->CiviRemote($params);
        codecept_debug($contacts);

        $I->assertEquals(0, $contacts['is_error'], 'Result is not an error.');

        // Why is it not sequential?
        $contact = reset($contacts['values']);
        $I->assertArrayHasKey('display_name', $contact);
    }

}
