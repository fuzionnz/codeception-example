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
    public function authenticatedContactGetShouldNotBeAnError(UnitTester $I)
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
    public function unauthenticatedContactGetShouldBeAnError(UnitTester $I)
    {
        $result = $I->CiviRemote([
            // Invalid API key.
            'api_key' => null,
            'entity' => 'Contact',
            'action' => 'get',
            'options' => [
                'limit' => 1,
            ]
        ]);
        $expected = 1;
        $actual = $result['is_error'];
        $I->assertEquals($expected, $actual);
    }
}
