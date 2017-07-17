<?php

use Faker\Factory;

namespace Step\Acceptance;

/**
 * Class CivicrmPage
 *
 * @package Step\Acceptance
 */
class CivicrmPage extends \AcceptanceTester
{

    /**
     * Bring this test to the foreground.
     *
     * @TODO Make this a config setting, and not need to call it in each test.
     */
    public function autofocus()
    {
        // Is it clunky to load this way?
        $config = \Codeception\Configuration::config();
        if (!empty($config['modules']['config']['CiviCRM']['switch_to_window'])){
            $this->switchToWindow();
        }

    }

    /**
     * See if an element is in the page via WebDriver.
     *
     * @param string $element
     *
     * @return bool
     */
    function detectElementPresent($element)
    {
        try {
            $this->getModule('WebDriver')->seeElement($element);
        } catch (\PHPUnit_Framework_AssertionFailedError $f) {
            return false;
        }
        return true;
    }

    /**
     * If there are others we can get them with increased craziness. Should
     * just put a .required on each required input, this JS will do for now.
     */
    public function fillCiviRequiredFields()
    {
        $this->executeJs("
               CRM.$('input', CRM.$('.crm-marker[title*=\"required\"]').closest('.crm-section')).each(function () {
                   if (this.value === '') {
                     this.value = '-';
                   }
               });
               CRM.$('select', CRM.$('.crm-marker[title*=\"required\"]').closest('.crm-section')).each(function () {
                   if (this.selectedIndex === 0) {
                     this.selectedIndex = 3;
                   }
               });

               CRM.$('input#cms_name.required').val('testuser.' + Date.now());
            ");
    }

    /**
     * Disable the "You have unsaved changes" warning.
     */
    public function disableWarningForUnsavedChanges()
    {
        $this->executeJs('window.onbeforeunload = null;');
    }

}