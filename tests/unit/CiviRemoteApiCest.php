<?php


class CiviRemoteApiCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    /**
     * @param \UnitTester $I
     *
     * @group blah
     */
    public function authenticatedCiviRemoteContactGetShouldNotBeAnError(UnitTester $I)
    {
        $result = $I->CiviRemote([
            'entity' => 'Contact',
            'action' => 'get',
            'options' => [
                'limit' => 1,
            ]
        ]);
        $expected = 0;
        $actual = $result['is_error'];
        $I->assertEquals($expected, $actual);
    }

    /**
     * Expected output with no parameters is
     * {"error_message":"Failed to authenticate key","is_error":1}
     *
     * This is using Codeception's PhpBrowser - fine for unit tests, but will
     * clash with browser acceptance tests.
     *
     * @group unauthenticated
     */
    public function unauthenticatedGetRequestShouldBeAnError(UnitTester $I)
    {
        $I->sendGET('', ['json' => 1]);
        $I->seeResponseContainsJson(['is_error' => 1]);
        $I->seeResponseContainsJson(['error_message' => 'Failed to authenticate key']);
    }

    /**
     * Check we can retrieve a contact from the API.
     *
     * @group authenticated
     */
    public function authenticatedContactGetShouldNotBeAnError(UnitTester $I)
    {
        $config = \Codeception\Configuration::config();
        $I->sendPOST('', [
            'api_key' => $config['modules']['config']['CiviHelper']['api_key'],
            'key' => $config['modules']['config']['CiviHelper']['site_key'],
            'entity' => 'Contact',
            'action' => 'Get',
            'json' => 1,
            'options' => [
                'limit' => 1,
            ]
        ]);
        $I->seeResponseContainsJson(['is_error' => 0]);
    }
}
