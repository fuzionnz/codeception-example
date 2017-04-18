<?php
/**
 * @file
 *
 */

namespace Codeception\Module;

use \Civi\civicrm_api3;
use Dompdf\Exception;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class CiviHelper extends \Codeception\Module
{
  static protected $civicrm_api3;

  /**
   * @param \AcceptanceTester $I
   * @return \Civi\civicrm_api3
   */
  public function CiviApi($creds)
  {
    if (!isset($this->civicrm_api3)) {
      foreach (['api_key', 'site_key', 'url'] as $required) {
        if (!isset($creds[$required])) {
          throw new InvalidConfigurationException('Missing required value: ' . $required);
        }
      }
      // class.api.php wants 'server' and 'path', OK.
      $url = parse_url($creds['url']);
      $config = [
        'server' => $url['scheme'] . '://' . $url['host'],
        'path' => $url['path'],
        'key' => $creds['site_key'],
        'api_key' => $creds['api_key'],
      ];
      $this->civicrm_api3 = new civicrm_api3($config);
    }
    return $this->civicrm_api3;
  }
}
