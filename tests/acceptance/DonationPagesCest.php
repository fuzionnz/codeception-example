<?php

use \Codeception\Example;
use \Step\Acceptance\ContributionPage;

class DonationPagesCest
{
    protected $civiRemoteApi;

    public function _before()
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function _inject()
    {
        $config = \Codeception\Configuration::config();
        $this->civiRemoteApi = new \CiviRemoteApi($config['modules']['config']['CiviRemoteApi']);
    }

    /**
     * This is an example dataProvider for contribution pages.
     *
     * @return array
     */
    protected function contributionPageProvider(AcceptanceTester $I)
    {
        $contributionPages = $I->CiviRemote([
            'entity' => 'Contact',
            'action' => 'get',
            'options' => [
                'limit' => 1,
            ],
        ]);

        // magic here ...

        return [
            [
                "id" => "1",
                "title" => "Help Support CiviCRM!",
                "pp" => "omnipay_PaymentExpress_PxPay",
                "amt_id" => "CIVICRM_QFID_5_8",
                "amt" => "50.00",
            ],
        ];
    }

    /**
     * @param AcceptanceTester $I , \Codeception\Example $example
     *
     * @group donation
     * @group dataprovider
     *
     */
    function AllDonationPages(\Step\Acceptance\ContributionPage $I)
    {
        $contributionPages = $I->CiviRemote([
            'entity' => 'Contact',
            'action' => 'get',
            'options' => [
                'limit' => 1,
            ],
        ]);

        $I->amOnPage("civicrm/contribute/transact?reset=1&id={$example['id']}");
        $I->see($example['title']);
    }

    /**
     * @example { "id": "1", "title": "Help Support CiviCRM!", "pp": "omnipay_PaymentExpress_PxPay", "amt_id": "CIVICRM_QFID_5_8", "amt": "50.00" }
     * @example { "id": "2", "title": "Help Support CiviCRM!", "pp": "omnipay_PaymentExpress_PxPay", "amt_id": "CIVICRM_QFID_4_6", "amt": "10.00" }
     *
     * @param AcceptanceTester $I, \Codeception\Example $example
     *
     * @group donation2
     * @group dataprovider
     */
    function AllDonationPagesByExample(\Step\Acceptance\ContributionPage $I, \Codeception\Example $example) {
        $I->amOnPage("civicrm/contribute/transact?reset=1&id={$example['id']}");
        $I->see($example['title']);
        $I->click('#' . $example['amt_id']);
        $I->fillCiviContributeFields();

        $I->wait(10);
    }

    /**
     * A DataProvider would be better here.
     * https://github.com/fuzionnz/codeception-example/issues/1
     *
     * Then we would separate the form completion logic (should get a single
     * test case to check out, eg "Donate $1 non-recurring using Auth.NET") from
     * the logic which obtains the set of cases.
     *
     * @param \AcceptanceTester $I
     *
     * @group donation
     */
    public function DonationPages(\Step\Acceptance\ContributionPage $I)
    {
        // DataProvider section.
        $params = [
            'entity' => 'ContributionPage',
            'action' => 'get',
            'is_active' => 1,
            // 'id' => 3,
            //            'options' => [
            //                'limit' => 1,
            //            ],
        ];
        $pages = $I->CiviRemote($params);
        $examples = [];

        // Iterate over pages to pick up payment processors.
        foreach ($pages['values'] as $page) {
            $example = [
                'page_id' => $page['id'],
                'page_title' => $page['title'],
                'page_url' => "civicrm/contribute/transact?reset=1&id={$page['id']}",
            ];

            // CiviCRM does not make price set data available to Contribute API?
            // If we want to test beyond the default price option, we need an
            // extension adding data via hook_civicrm_apiWrappers() or to
            // or to retrieve options from the DOM.
            // @see CRM-20503

            // Check if "amount block" is active, and whether we got back a price
            // set from the API.
//            if ($page['amount_block_is_active']) {
            if (isset($page['min_amount']) && $page['min_amount'] > 0) {
                $example['other_amount'] = $page['min_amount'];
            } else {
                $example['other_amount'] = 1;
            }
//            }

            if (isset($page['payment_processor'])) {
                // If API returned a single value, make it an array.
                $processor_ids = (is_array($page['payment_processor'])) ?
                    $page['payment_processor'] : [$page['payment_processor']];

                foreach ($processor_ids as $payment_processor_id) {
                    $params = [
                        'entity' => 'PaymentProcessor',
                        'action' => 'get',
                        'id' => $payment_processor_id,
                        'sequential' => true,
                    ];
                    $payment_processor = $I->CiviRemote($params);
                    $payment_processor = $payment_processor['values'][0];
                    $example['payment_processor_id'] = $payment_processor_id;
                    $example['payment_processor_class_name'] = $payment_processor['class_name'];
                    $example['payment_processor_billing_mode'] = $payment_processor['billing_mode'];
                    $example['payment_processor_is_recur'] = $payment_processor['is_recur'];

                    $params = [
                        'entity' => 'PaymentProcessorType',
                        'action' => 'get',
                        'id' => $payment_processor['payment_processor_type_id'],
                        'sequential' => 1,
                    ];
                    $payment_processor_type = $I->CiviRemote($params);
                    $payment_processor_type = $payment_processor_type['values'][0];
                    $example['payment_processor_type_name'] = $payment_processor_type['name'];
                    $example['payment_processor_type_title'] = $payment_processor_type['title'];

                    $examples[] = $example;
                    codecept_debug(['$example' => $example]);
                }
            }
        }

        // Alright, we've built a list of contribution pages examples. Let's test them!
        codecept_debug(['$examples' => $examples]);

        foreach ($examples as $example) {
            $I->amOnPage($example['page_url']);
            $I->see($example['page_title']);

            // Where there's no default amount & the other amount is required,
            // contribute the minimum amount.
            $I->fillAmountFields($example['other_amount']);

            // Complete the required fields.
            $I->fillCiviContributeFields();

            $I->completeTransaction([
                'mode' => 'live',
                'payment_processor_id' => $example['payment_processor_id'],
                'payment_processor_class_name' => $example['payment_processor_class_name'],
            ]);

            // Because we're not calling this method repeatedly, need to reset
            // session before proceeding.
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
