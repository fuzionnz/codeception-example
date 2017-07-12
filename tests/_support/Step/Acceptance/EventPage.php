<?php

use Faker\Factory;

namespace Step\Acceptance;

/**
 * Class EventPage
 *
 * @package Step\Acceptance
 */
class EventPage extends ContributionPage
{

  /**
   * Fill essential CiviCRM Contribute fields (first name, last name, email)
   * using Faker data.
   */
  public function fillCiviEventFields()
  {
    $I = $this;
    $faker = \Faker\Factory::create();

    // @TODO Consistent selectors with Contribute page.
    $I->fillField('#email-Primary', $faker->safeEmail());
    $I->executeJS("CRM.$('input[id*=\"first_name\"]').val(" . json_encode($faker->firstName()) . ");");
    $I->executeJS("CRM.$('input[id*=\"last_name\"]').val(" . json_encode($faker->lastName()) . ");");
  }

}