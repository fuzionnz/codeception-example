<?php

use Faker\Factory;

namespace Step\Acceptance;

class ContributionPage extends \AcceptanceTester
{

  /**
   * I'd like this to be a dataProvider ... but don't yet know how to pass
   * a working CiviCRM API config into a dataprovider.
   *
   * @return array
   */
  function getContributionPages()
  {
    $client = $this->CiviApi();
    // Get the contribution pages which are enabled.
    $client->ContributionPage->Get([
      'is_active' => 1,
      'options' => [
        'sequential' => 1,
        'limit' => 1,
      ],
    ]);
    $data = [];
    foreach ($client->values as $page) {
      $data[$page->id] = [
        'id' => $page->id,
        'title' => $page->title,
        'pp' => $this->getPaymentProcessorName($page),
        'amt_id' => $this->getAmountId($page),
        'amt' => $this->getAmount($page),
      ];

    }
    return $data;
  }


  protected function getPaymentProcessorName($page) {
    print_r($page);
    return 'omnipay_PaymentExpress_PxPay';
  }

  protected function getAmountId($page) {
    return 'CIVICRM_QFID_5_8';
  }

  protected function getAmount($page) {
    return '50.00';
  }

  public function fillCiviContributeFields()
  {
    $faker = \Faker\Factory::create();
    $I = $this;

    // Some fields are easy to match.
    $I->fillField('.email.required', $faker->safeEmail());

    // Some are slightly trickier. Required ID could be got from API, or we
    // could just add a .first-name class etc to Civi's forms.
    $I->executeJS("CRM.$('input[class*=\"_first_name\"]').val('" . $faker->firstName() . "');");
    $I->executeJS("CRM.$('input[class*=\"_last_name\"]').val('" . $faker->lastName() . "');");

    // If there are others we can get them with increased craziness. Should
    // just put a .required on each required input, this JS covers it for now.
    $I->executeJs("
      CRM.$('input', CRM.$('.crm-marker[title*=\"required\"]').closest('.crm-section')).each(function () { if (this.value === '') { this.value = 'Dummy'; } });
      CRM.$('select', CRM.$('.crm-marker[title*=\"required\"]').closest('.crm-section')).each(function () { if (this.selectedIndex === 0) { this.selectedIndex = 3; } });
    ");

    // Get these infos from the payment provider helper.
    // External site PPs will have a whole other page helper?
    //$I->fillField('.creditcard', '4111111111111111');
    //$I->fillField('cvv2', '111');
    //$I->selectOption('#credit_card_exp_date_M', '12');
    //$I->selectOption('#credit_card_exp_date_Y', date('Y')+1);

      // Submit!
    $I->click('#_qf_Main_upload-bottom');

    // If the contribute page has a confirm screen, check for expected details.
    $I->see('Please verify');
    $I->click('#_qf_Main_upload-bottom');

    // Now we're on to DPS (for this particular example).
  }

  public function checkOut($provider_class)
  {
    switch ($provider_class)
    {
      case ''
    }
  }

}