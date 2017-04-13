<?php
/**
 * @file
 *
 */

namespace Codeception\Module;

class CiviRemoteApiHelper extends \Codeception\Module
{
  protected $civicrm_api3;

  public function getCivicrmApiClass()
  {
    require_once $this->getConfig('path_civicrm_settings');
    require_once $this->getConfig('path_civicrm_api');
    $civicrm_api3_class = new \civicrm_api3();
    die(gettype($civicrm_api3_class));
    return civicrm_api3_class;
  }

  public function civicrm_api3()
  {
    require_once $this->config['site_directory'] . '/civicrm.settings.php';
    require_once $this->config['civicrm_directory'] . '/api/api.php';

    $args = func_get_args();
    return call_user_func_array('civicrm_api3', $args);
  }
}
