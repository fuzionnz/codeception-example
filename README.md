# Codeception Example

## Getting started

## Configuration

### CiviCRM configuration

* Ensure that all user(s) you wish to access the API as have an API key in the `civicrm_contact` table. (If you're just getting started, just configure your primary admin user.)

### Test suite configuration

* In the `tests` directory, copy `acceptance.suite.example.yml` to `acceptance.suite.yml` and copy `functional.suite.example.yml` to `functional.suite.yml`. (You might as well do this for `unit.suite.yml` also, but that suite isn't yet used in this example.)  
* in `acceptance.suite.yml` and `functional.suite.yml`, configure:
  * For `CiviHelper` section: 
    * `site_key` is the value of `CIVICRM_SITE_KEY` in `civicrm.settings.php`
    * `api_key` is the API key for your user from CiviCRM. 
    * `url` is the full URL to your CiviCRM API endpoint (`rest.php`)
  * For `WebDriver` section (`acceptance.suite.yml` only):
    * `url` is the base URL of your site
    * `browser` is the browser you wish to use (firefox, chrome etc)

### Verify your API endpoint config

Take a moment to verify that the config as above works. Visit the CiviCRM API Explorer and generate a simple API request (`Contact.get` with no parameters is fine), then adjust the `api_key` and `site_key` values based on what's above. Request this URL from a logged out client (`curl` is fine in CLI) and confirm you get a response with data.

### Bootstrap Codeception

Let's get some code generated in here.

    codecept bootstrap

### What's in here

`tests/_support/Step/Acceptance/ContributionPage.php`

This is a Codeception Page Helper - it wraps up common functionality for a page (ish). Helper methods like `detectOtherAmountIsRequired`, `etectOtherAmountIsRequired`, `disableWarningForUnsavedChanges`, `fillAmountFields`, `fillCiviEventFields`, `fillCiviContributeFields`, `fillCiviRequiredFields`, `completeTransaction`.

Most of those are fairly explanator by their names.

`completeTransaction` uses a switch statement to branch based on the payment providers. This would be better restructured into separate helpers loaded based on the payment provider. Want to help me understand DI? I'd love to :)
 
`tests/_support/CiviRemoteApi.php`

This is a Codeception Module - it provides additional methods to the Acceptance Tester, in this case the ability to call CiviCRM API remotely via CiviCRM's `extern/rest.php`. If this is loaded, then the acceptance tester (`$I`) will have that method available. The details of CiviCRM API access are retrieved from the suite's config `.yml` (or can be passed in as part of `$params`).

A simple demo of this is available in `tests/functional/CiviRemoteCest.php`.

There are a couple of additional "decoy" helpers, `CiviHelper` and `CiviRemoteAPIHelper`. Ignore these - they are just early efforts and should be removed.

`tests/acceptance/DonationPagesCest.php`

This is an acceptance test which utilises the above Codeception functionality.

`contributionPageProvider` is a dataprovider method which uses CiviRemoteApi to retrieve a list of testable Contribution Pages.
 
`AllDonationPages` is a test which tests all the donation pages; the `@dataprovider contributionPageProvider` instructs this test to run once with each example provided by the dataprovider. 

`AllDonationPagesByExample` demonstrates doing similarly, but uses the `@example` annotation instead to provide test examples.

`DonationPages` is possibly an older example.