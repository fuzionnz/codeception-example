<?php


class ProfileApiCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    /**
     * @param \FunctionalTester $I
     *
     * @group profileapi
     */
    public function GetMultipleProfiles(FunctionalTester $I)
    {
        // Ensure we can list profiles via API.
        $result = $I->CiviRemote([
            'entity' => 'UFGroup',
            'action' => 'get',
        ]);

        codecept_debug($result);

        $I->assertArrayNotHasKey('error_message', $result,'Error');
        $I->assertEquals(0, $result['is_error'], 'Result is not an error.');

        $contact = reset($result['values']);
        $I->assertArrayHasKey('title', $contact);
    }
}
