<?php

namespace Helper;
use GuzzleHttp\Client;

/**
 * Class CiviRemote
 *
 * Helper class to interact with the CiviCRM Remote API (rest.php).
 *
 * Adds a method $I->CiviRemote() which accepts APIv3 style parameters.
 *
 * By default obtains credentials from the suite's .yml configuration, accepts
 * credentials passed in via $params as api_key / key to override suite config.
 *
 * @TODO Get config more directly.
 * Would be nice to use $this->config to get CiviRemote config from
 * codeception.yml / suitename.suite.yml here, but not working for me ...
 *
 * @package Helper
 */
class CiviRemote extends \Codeception\Module
{
    // protected $requiredFields = ['api_key', 'site_key', 'url'];
    // protected $config = ['host' => '127.0.0.1', 'port' => '4444'];

    /**
     * @TODO Make it really clear when API call fails.
     *
     * Otherwise we end up with "dataProvider invalid or not callable" when
     * this throws an exception, which is a timewaster.
     *
     * @param array $params
     *   CiviCRM APIv3 parameters array.
     *
     * @return mixed
     */
    public function CiviRemote($params) {
        $config = \Codeception\Configuration::config();
        $creds = [
            'api_key' => $config['modules']['config']['CiviRemote']['api_key'],
            'key' => $config['modules']['config']['CiviRemote']['site_key'],
        ];
        $client = new Client([
            'base_uri' => $config['modules']['config']['CiviRemote']['url'],
        ]);

        $params = array_merge($creds, $params);
        $request = $client->post('', ['form_params' => $params, 'query' => 'json=1']);

        // Guzzle throws exception on HTTP error, we don't need to check that.
        // Need to check returned values are valid JSON though.
        $json = (string) $request->getBody();
        // Force to an array when decoding.
        $data = json_decode($json, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            // http://php.net/manual/en/json.constants.php
            $msg = 'Error decoding CiviRemote response (JSON error #' .
                json_last_error() . ': ' .
                json_last_error_msg() .
                ')';
            codecept_debug('JSON: ' . $json);
            throw new \Exception($msg);
        }
        return (array) $data;
    }
}
