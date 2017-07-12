<?php

use \Codeception\Example;
use \Step\Acceptance\ContributionPage;

class EventPagesCest
{

    public function _before()
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function _inject()
    {
    }

    /**
     * Provide all active event pages.
     *
     * @return array
     */
    protected static function eventPageProvider()
    {
        $config = \Codeception\Configuration::config();
        $civiRemoteApi = new \CiviRemoteApi($config['modules']['config']['CiviRemoteApi']);

        $params = [
            'entity' => 'Event',
            'action' => 'get',
            'is_active' => 1,
            'is_online_registration' => 1,
            'is_template' => 0,
            'options' => [
              // 'limit' => 1,
            ],
        ];
        $events = $civiRemoteApi->CiviRemote($params);
        $examples = [];

        // Iterate over pages to pick up payment processors.
        foreach ($events['values'] as $event) {
            // Skip if the event has already expired.
            // @TODO APIv3 query for "Events with no or future expiry date"?
            if (strtotime($event['end_date']) <= time()) {
                continue;
            }

            $example = [
                'event_id' => $event['id'],
                'event_title' => $event['title'],
                'event_url' => "civicrm/event/register?reset=1&id={$event['id']}",
            ];

            if (isset($event['payment_processor'])) {
                // If API returned a single value, make it an array.
                $processor_ids = (is_array($event['payment_processor'])) ?
                    $event['payment_processor'] : [$event['payment_processor']];

                foreach ($processor_ids as $payment_processor_id) {
                    $params = [
                        'entity' => 'PaymentProcessor',
                        'action' => 'get',
                        'id' => $payment_processor_id,
                        'sequential' => true,
                    ];
                    $payment_processor = $civiRemoteApi->civiRemote($params);
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
                    $payment_processor_type = $civiRemoteApi->CiviRemote($params);
                    $payment_processor_type = $payment_processor_type['values'][0];
                    $example['payment_processor_type_name'] = $payment_processor_type['name'];
                    $example['payment_processor_type_title'] = $payment_processor_type['title'];

                    $examples[] = $example;
                }
            }
        }

        return $examples;
    }

    /**
     * @param AcceptanceTester $I, \Codeception\Example $example
     *
     * @dataprovider eventPageProvider
     *
     * @group event
     */
    function EventPages(\Step\Acceptance\EventPage $I, \Codeception\Example $example)
    {
      // $I->focus();

      $I->amOnPage($example['event_url']);
        $I->see($example['event_title']);

        $I->disableWarningForUnsavedChanges();

        // Complete the required fields.
        $I->fillCiviEventFields();

        $I->completeTransaction([
            'mode' => 'live',
            'payment_processor_id' => $example['payment_processor_id'],
            'payment_processor_class_name' => $example['payment_processor_class_name'],
        ]);
    }
}
