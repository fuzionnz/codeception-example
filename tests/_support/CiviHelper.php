<?php
/**
 * @file
 *
 */

namespace Codeception\Module;

use \Civi\civicrm_api3;
use \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class CiviHelper extends \Codeception\Module
{
  static protected $civicrm_api3;

  protected $requiredFields = [
    'api_key',
    'site_key',
    'url'
  ];

  protected $config = [
    'api_key' => '',
    'site_key' => '',
    'url' => 'http://localhost/sites/all/modules/civicrm/extern/rest.php',
  ];

  /**
   * @param \AcceptanceTester $I
   * @return \Civi\civicrm_api3
   */
  public function CiviApi()
  {
    if (!isset($this->civicrm_api3) || empty($this->civicrm_api3)) {
      // class.api.php wants 'server' and 'path', OK.
      $url = parse_url($this->config['url']);
      $config = [
        'server'  => $url['scheme'] . '://' . $url['host'],
        'path'    => $url['path'],
        'key'     => $this->config['site_key'],
        'api_key' => $this->config['api_key'],
      ];
      $this->civicrm_api3 = new civicrm_api3($config);
    }
    return $this->civicrm_api3;
  }
}
