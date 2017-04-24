<?php

use \Codeception\Example;

class DonationPagesCest
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
   * @return array
   */
  protected function contributionPageProvider()
  {
    $client = $I->CiviApi();
    // Get the contribution pages which are enabled.
    $client->ContributionPage->Get([
      'is_active' => 1,
      'options' => [
        'sequential' => 1,
        'limit' => 1,
      ],
    ]);
    print_r($client->values);
    // Convert to this format.
    return [
      [
        'id'=>"1",
        'title'=> "Help Support CiviCRM!",
        'payment_processor_name' => 'omnipay_PaymentExpress_PxPay',
        'amount' => '10',
        'amount_field' => '3-2-4',
        'data' => '',
      ],
    ];
  }

  /**
   * @example { "id": "1", "title": "Help Support CiviCRM!", "pp": "omnipay_PaymentExpress_PxPay", "amt_id": "CIVICRM_QFID_5_8", "amt": "50.00" }
   * @example { "id": "2", "title": "Help Support CiviCRM!", "pp": "omnipay_PaymentExpress_PxPay", "amt_id": "CIVICRM_QFID_4_6", "amt": "10.00" }
   *
   * @param AcceptanceTester $I, \Codeception\Example $example
   *
   * @group edsf
   */
  function AllDonationPages(\Step\Acceptance\ContributionPage $I, \Codeception\Example $example) {
    $I->amOnPage("civicrm/contribute/transact?reset=1&id={$example['id']}");
    $I->see($example['title']);
    $I->click('#' . $example['amt_id']);
    $I->fillCiviContributeFields();

    $I->wait(10);
  }

  /**
   * @param \AcceptanceTester $I
   *
   * @group donation
   */
  public function DonationPages(\Step\Acceptance\ContributionPage $I)
  {
    $client = $I->CiviApi();

    // Get the contribution pages which are enabled.
    $client->ContributionPage->Get([
      'is_active' => 1,
      'options' => [
        'sequential' => 1,
        'limit' => 1,
      ],
    ]);

    // Iterate through all the enabled contribution pages, verify that they have
    // the expected title and a submit button.
    //print_r($client->values);
    $contribution_pages = (array)$client->values;
    //print_r($contribution_pages);
    if (!empty($contribution_pages)) {
      foreach ($contribution_pages as $contribution_page) {
        print_r($contribution_page);
        $I->amOnPage("civicrm/contribute/transact?reset=1&id={$contribution_page->id}");
        $I->see($contribution_page->title);

        // Complete the required fields.
        $I->fillCiviContributeFields();

        // Not sure how to extract available contribution amounts from CiviCRM.
        // But ... we can extract them from the DOM! @TODO
        // For now, just use default amount.

        // Ex: Put $1 into the "Other" field.
        // $I->fillField('.other_amount-content .crm-form-text', 1);

        // THis all goes over in EntityExtra I reckon. Whoo!
        // Get the Payment Processor, then ...
        //print_r($contribution_page);
        if (isset($contribution_page->payment_processor)) {
          $payment_processor_id = $contribution_page->payment_processor;
          if ($client->PaymentProcessor->Get([ 'id' => $contribution_page->payment_processor ])) {
            // Actually pages have multiple pps. For now keep page config simple.
            $payment_processor = $client->values[0];

            // Get the Payment Processor Type.
            if ($client->PaymentProcessorType->Get(['id' => $payment_processor->payment_processor_type_id])) {
              $payment_processor_type = $client->values[0];

              // Argh we still need to ID the processor since this just says "Omnipay".
              switch ($payment_processor_type->name) {
                case 'omnipay_PaymentExpress_PxPay':
                   print_r($payment_processor_type->name);

                case 'Dummy':
              }
            }
          }
        }

        // $I->see(print_r($client->values[0]));

        $I->wait(30);
      }
    }


    // required_values is the civicrm field names (from civicrm pages)
    // + any associated with this donation page
    // + accept a generator of some sort - can be civicrm api get all donation pages
    // + dynamically match any field marked "required" as populsyttyrf

    // Run a transaction - for each payment processor on the page.
    // API doesn't reveal processors?! So we will use the default processor for
    // now. Make it easy to add second test for same page with diff processor?

    // These are fetchable via lookup (use civicrm json/http).

  }
}
