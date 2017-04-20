<?php


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
   * @param \AcceptanceTester $I
   *
   * @group donation
   */
  public function DonationPages(AcceptanceTester $I)
  {
    $credentials = [
      'api_key' => $I->getConfig('api_key'),
      'site_key' => $I->getConfig('site_key'),
      'url' => $I->getConfig('url'),
    ];

    $client = $I->CiviApi($credentials);

    // Get the contribution pages which are enabled.
    $client->ContributionPage->Get([
      'is_active' => 1,
      'options' => [
        'sequential' => 1,
        'limit' => 10,
      ],
    ]);

    // Iterate through all the enabled contribution pages, verify that they have
    // the expected title and a submit button.
    foreach ($client->values as $contribution_page) {
      $I->amOnPage("civicrm/contribute/transact?reset=1&id={$contribution_page->id}&action=preview");
      $I->see($contribution_page->title);

      
    }

    // required_values is the civicrm field names (from civicrm pages)
    // + any associated with this donation page
    // + accept a generator of some sort - can be civicrm api get all donation pages
    // + dynamically match any field marked "required" as populsyttyrf

    // Run a transaction - for each payment processor on the page.

    // These are fetchable via lookup (use civicrm json/http).


  }
}
