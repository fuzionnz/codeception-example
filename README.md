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

