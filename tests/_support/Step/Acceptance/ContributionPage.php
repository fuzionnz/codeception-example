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

    /**
     * If there are others we can get them with increased craziness. Should
     * just put a .required on each required input, this JS will do for now.
     */
    public function fillCiviRequiredFields() {
        $I = $this;
        $I->executeJs("
           CRM.$('input', CRM.$('.crm-marker[title*=\"required\"]').closest('.crm-section')).each(function () { 
             if (this.value === '') { 
               this.value = 'Dummy'; 
             } 
           });
           CRM.$('select', CRM.$('.crm-marker[title*=\"required\"]').closest('.crm-section')).each(function () { 
             if (this.selectedIndex === 0) { 
               this.selectedIndex = 3; 
             } 
           });
        ");
    }

    /**
     * Fill essential CiviCRM Contribute fields (first name, last name, email)
     * using Faker data.
     */
    public function fillCiviContributeFields()
    {
        $faker = \Faker\Factory::create();
        $I = $this;

        // Some fields are easy to match.
        $I->fillField('.email.required', $faker->safeEmail());

        // Some are slightly trickier. Required ID could be got from API, or we
        // could just add a .first-name class etc to Civi's forms.
        $I->executeJS("CRM.$('input[id*=\"_first_name\"]').val(" . json_encode($faker->firstName()) . ");");
        $I->executeJS("CRM.$('input[id*=\"_last_name\"]').val(" . json_encode($faker->lastName()) . ");");
    }

    /**
     * Complete a checkout using a specific payment processor type.
     *
     * @param array $details
     */
    public function completeTransaction($details)
    {
        $faker = \Faker\Factory::create();
        $I = $this;

        // If we're in test mode, we need to increment the payment processor ID
        // to select the correct option.
        if ($details['mode'] == 'test')
        {
            $details['payment_processor_id']++;
        }

        // Select the payment method.
        $I->click("#CIVICRM_QFID_{$details['payment_processor_id']}_payment_processor_id");

        // May need a pause here to allow checkout to load?
        $I->wait(2);

        switch ($details['payment_processor_class_name'])
        {
            // If running into contribution ID conflicts with Omnipay, can work
            // around contribution_id vs transaction_id conflation by raising
            // mysql -e 'alter table civicrm_contribution auto_increment=1000'
            // @see
            case 'Payment_OmnipayMultiProcessor':
            case 'omnipay_PaymentExpress_PxPay':
                // "Confirm Contribution"
                $I->click('#_qf_Main_upload-bottom');

                // We are on the confirm page now.
                // @TODO This is a config option?
                $I->see('Please verify the information below carefully.');
                $I->click('#_qf_Confirm_next-top');

                // PxPay checkout.
                $I->fillField('input[name=CardNumber]', '4111111111111111');
                $I->fillField('input[name=CardHolderName]', $faker->name());
                $I->fillField('input[name=Cvc2]', '111');
                $I->click('button.DpsPxPayOK');

                $I->see('Transaction Approved');
                $I->click('a.DpsPxPayOK');

                break;

            case 'Dummy':
            default:
                codecept_debug(['$details' => $details]);
                $I->selectOption('#credit_card_type', 'Visa');
                $I->fillField('#credit_card_number', '4111111111111111');
                $I->fillField('#cvv2', '111');
                $I->selectOption('#credit_card_exp_date_M', '12');
                $I->selectOption('#credit_card_exp_date_Y', date('Y') +  1);
                $I->fillCiviRequiredFields();
                $I->click('#_qf_Main_upload-bottom');
                $I->click('#_qf_Confirm_next-top');
        }
    }

    /**
     * Clear the CiviCRM "you are leaving the page!" message.
     */
    // public function

}