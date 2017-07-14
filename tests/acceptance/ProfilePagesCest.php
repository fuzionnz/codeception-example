<?php

use \Codeception\Example;
use \Step\Acceptance\ContributionPage;

class ProfilePagesCest
{

    public function _before()
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function _inject()
    {
    }

    /**
     * Provide all active profile pages.
     *
     * Might be that we should have providers for:
     * - contactCreateProfileProvider
     * - userCreateProfileProvider
     * - contactUpdateProfileProvider (via magic login link)
     *
     * @return array
     */
    protected static function profilePageProvider()
    {
        $config = \Codeception\Configuration::config();
        $civiRemoteApi = new \CiviRemoteApi($config['modules']['config']['CiviRemoteApi']);

        $params = [
            'entity' => 'UFGroup',
            'action' => 'get',
            'is_active' => 1,
            'is_reserved' => 0,
            // 'id' => 12,
            // group_type (comma separated list of contact types)
            //
            'options' => [
              // 'limit' => 1,
            ],
        ];
        $profiles = $civiRemoteApi->CiviRemote($params);
        $examples = [];

        if (!empty($profiles['values'])) {
          // Iterate over pages to pick up payment processors.
          foreach ($profiles['values'] as $profile) {
            $example = [
              'profile_id' => $profile['id'],
              'profile_title' => $profile['title'],
              'profile_url' => "civicrm/profile/create?gid={$profile['id']}",
            ];

            // Other profile setup?
            $examples[] = $example;
          }

        }
        return $examples;
    }

    /**
     * @param AcceptanceTester $I , \Codeception\Example $example
     *
     * @dataprovider profilePageProvider
     *
     * @group profile
     *
     */
    function ProfilePages(\Step\Acceptance\ProfilePage $I, \Codeception\Example $example)
    {
        // $I->focus();

        $I->amOnPage($example['profile_url']);
        $I->see($example['profile_title']);

        $I->disableWarningForUnsavedChanges();

        // Complete the required fields.
        $I->fillCiviProfileFields();

        $I->see($example['profile_title']);
        // Presumably not hardcoded?
        $I->see('Your information has been saved.');
    }
}
