<?php

use Faker\Factory;

namespace Step\Acceptance;

/**
 * Class ProfilePage
 *
 * @package Step\Acceptance
 */
class ProfilePage extends CivicrmPage
{

    /**
     * Fill essential CiviCRM Contribute fields (first name, last name, email)
     * using Faker data.
     */
    public function fillCiviProfileFields()
    {
        $I = $this;
        $faker = \Faker\Factory::create();

        $I->executeJS("CRM.$('input[id*=\"first_name\"]').val(" . json_encode($faker->firstName()) . ");");
        $I->executeJS("CRM.$('input[id*=\"last_name\"]').val(" . json_encode($faker->lastName()) . ");");

        $values = [
          '#email-Primary' => $faker->safeEmail(),
          '#cms_name' => $faker->userName(),
        ];
        // We do actually want a try/catch for each field, in case one is not
        // present and we need to move on to the next.
        // We catch and ignore these assertions.
        foreach ($values as $selector => $value)
        {
            try
            {
                $I->fillField($selector, $value);
            }
            catch (\PHPUnit_Framework_AssertionFailedError $e) { /* don't care */ }
        }

        $I->fillCiviRequiredFields();

        $I->completeSubmission();
    }


  /**
     * Complete a submission.
     *
     * @param array $details
     */
    public function completeSubmission()
    {
        $faker = \Faker\Factory::create();
        $I = $this;

        $I->click('.crm-form-submit.default');
    }
}