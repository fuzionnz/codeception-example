<?php

use Faker\Factory;

namespace Step\Acceptance;

/**
 * Class ContributionPage
 *
 * @package Step\Acceptance
 */
class ContributionPage extends CivicrmPage
{

    /**
     * Detect whether there is a required "other" amount field.
     */
    public function detectOtherAmountIsRequired()
    {
        $I = $this;
        return $I->executeJs('return CRM.$(\'.other_amount-content input.required\').length !== 0;');
    }

    /**
     * Detect whether there is a required "username" field.
     */
    public function detectUsernameIsRequired()
    {
        $I = $this;
        return $I->executeJs('return CRM.$(\'.other_amount-content input.required\').length !== 0;');
    }

    /**
     * Detect if contribution page has multiple processors available.
     *
     * @return boolean
     */
    public function detectMultiplePaymentProcessorsAvailable()
    {
        $I = $this;
        return $I->executeJs('return CRM.$(\'input[name="payment_processor_id"]\')[0].type !== "hidden";');
    }

    /**
     * Fill amount fields. May need to be filled out; currently assumes most
     * donation forms have a default option selected.
     *
     * @param $amount
     */
    public function fillAmountFields($amount)
    {
        $I = $this;
        if ($I->detectOtherAmountIsRequired()) {
            $I->fillField('.other_amount-content input.required', $amount);
        }
    }

    /**
     * Fill essential CiviCRM Contribute fields (first name, last name, email)
     * using Faker data.
     */
    public function fillCiviContributeFields()
    {
        $I = $this;
        $faker = \Faker\Factory::create();

        $safeEmail = $faker->safeEmail();
        $firstName = $faker->firstName();
        $lastName = $faker->lastName();

        // Some fields are easy to match.
        $I->fillField('.email.required', $safeEmail);

        // Some are slightly trickier. Required ID could be got from API, or we
        // could just add a .first-name class etc to Civi's forms.
        $I->executeJS("CRM.$('input[id*=\"first_name\"]').val(" . json_encode($firstName) . ");");
        $I->executeJS("CRM.$('input[id*=\"last_name\"]').val(" . json_encode($lastName) . ");");
        $I->executeJS("CRM.$('input[id*=\"email\"]').val(" . json_encode($safeEmail) . ");");
    }

    /**
     * Complete a checkout using a specific payment processor type.
     * This may warrant its own Page helper.
     *
     * @param array $details
     */
    public function completeTransaction($details)
    {
        $faker = \Faker\Factory::create();
        $I = $this;

        // If we're in test mode, we need to increment the payment processor ID
        // to select the correct option.
        if ($details['mode'] == 'test') {
            $details['payment_processor_id']++;
        }

        // If there's only one payment processor on the current page, it's a
        // hidden element. If not, then we need to select.
        if ($I->detectMultiplePaymentProcessorsAvailable()) {
            $payment_processor_radio = "#CIVICRM_QFID_{$details['payment_processor_id']}_payment_processor_id";
            // @TODO: Should be able to $I->click() ... is it hidden sometimes?
            // So use JS to not fail test.
            $I->executeJs('return CRM.$(\'' . $payment_processor_radio . '\').click();');
            $I->waitForJS("return CRM.$.active == 0;", 60);
        }

        switch ($details['payment_processor_class_name']) {
            // If running into contribution ID conflicts with Omnipay, can work
            // around contribution_id vs transaction_id conflation by raising
            // mysql -e 'alter table civicrm_contribution auto_increment=1000'
            // @see https://github.com/eileenmcnaughton/nz.co.fuzion.omnipaymultiprocessor/issues/26
            case 'Payment_OmnipayMultiProcessor':
            case 'omnipay_PaymentExpress_PxPay':
                // "Confirm Contribution"
                $I->click('.crm-form-submit.default');

                if (!empty($details['has_confirm'])) {
                    $I->see('Please verify the information below carefully.');
                    $I->click('.crm-form-submit.default');
                }

                // PxPay checkout.
                $I->fillField('input[name=CardNumber]', '4111111111111111');
                $I->fillField('input[name=CardHolderName]', $faker->name());
                $I->fillField('input[name=Cvc2]', '111');
                $I->click('button.DpsPxPayOK');

                $I->see('Transaction Approved');
                $I->click('a.DpsPxPayOK');
                break;

            case 'Payment_Paystation':
                // "Confirm Contribution"
                $I->fillCiviRequiredFields();
                $I->click('.crm-form-submit.default');
                $I->click('#card_type_MASTERCARD');
                $I->fillField('#cardnumber', '5123456789012346');
                $I->fillField('#cardverificationcode', '123');
                $I->selectOption('#expirymonth', date('m'));
                $I->selectOption('#expiryyear', date('Y'));
                $I->fillField('#cardholder', $faker->name());
                $I->click('#pay_button');
                break;

            case 'uk.co.vedaconsulting.payment.gocardlessdd':
                // "Confirm Contribution"
                $I->click('.crm-form-submit.default');

            case 'Payment_AuthorizeNet':
            case 'Payment_Elavon':
            case 'Payment_eWAY':
            case 'Payment_FirstData':
            case 'Payment_Google':
            case 'Payment_PayflowPro':
            case 'Payment_Realex':
            case 'Payment_PaymentExpress':
            case 'Payment_PayJunction':

            // This is the classname for all of:
            // * PayPal - Website Payments Pro - "PayPal"
            // * PayPal - Express - "PayPal_Express"
            // * PayPal - Website Payments Standard - "PayPal_Standard"
            case 'Payment_PayPalImpl':

                break;

            case 'Payment_Dummy':
            case 'Dummy':
            default:
                // The credit card type option is a required <select> when there
                // are multiple processors, but automatic when only one.
                $cc_type_select = $I->executeJs('return CRM.$(\'select#credit_card_type:visible\').length;');
                if ($cc_type_select) {
                    $I->selectOption('#credit_card_type', 'Visa');
                }
                $I->fillField('#credit_card_number', '4111111111111111');
                $I->fillField('#cvv2', '111');
                $I->selectOption('#credit_card_exp_date_M', 12);
                $I->selectOption('#credit_card_exp_date_Y', date('Y') + 1);
                $I->fillCiviRequiredFields();
                if (!empty($details['has_confirm'])) {
                    $I->click('.crm-form-submit.default');
                }
                $I->click('.crm-form-submit.default');
        }
    }
}