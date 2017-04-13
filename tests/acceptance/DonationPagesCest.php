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

    public function DonationPages(AcceptanceTester $I)
    {
      // Read yml array of donation pages.

      // required_values is the civicrm field names (from civicrm pages)
      // + any associated with this donation page
      // + accept a generator of some sort - can be civicrm api get all donation pages
      // + dynamically match any field marked "required" as populsyttyrf

      // Run a transaction - for each payment processor on the page.

      // These are fetchable via lookup (use civicrm json/http).


    }
}
