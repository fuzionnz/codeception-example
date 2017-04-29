<?php
/**
 * @file
 *
 */

namespace Codeception\Module;

use \Civi\civicrm_api3;
use Dompdf\Exception;
use \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class CiviHelper extends \Codeception\Module
{
  protected $civicrm_api3;

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
  public function CiviApi(\AcceptanceTester $I)
  {
    $config = \Codeception\Configuration::config();
    if (!isset($config['modules']['config']['CiviHelper']))
    {
      throw new Exception('no config');
    }
    codecept_debug($config['modules']['config']['CiviHelper']);

    // We could allow alternate creds to be passed in.
//    if (!isset($this->civicrm_api3) || empty($this->civicrm_api3)) {
      // class.api.php wants 'server' and 'path', OK.
      $url = parse_url($this->config['url']);
      $config = [
        'server'  => $url['scheme'] . '://' . $url['host'],
        'path'    => $url['path'],
        'key'     => $this->config['site_key'],
        'api_key' => $this->config['api_key'],
      ];
      return new civicrm_api3($config);
//    }
    // return $this->civicrm_api3;
  }

}
