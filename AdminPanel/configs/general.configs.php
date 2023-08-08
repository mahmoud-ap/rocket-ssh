<?php 

if (!defined('PATH')) die();

/**
 * Project Info
 */
$configs['project']['name'] = 'Xpanle';
$configs['project']['version'] = '1.0.0';

/**
 * Base Url
 * with trailing slash
 */
$configs['url'] = 'http://localhost:8001/';

/**
 * Timezone
 */
$configs['timezone'] = 'Asia/Tehran';

/**
 * Error Reporting Status
 * Bool
 */
$configs['show_errors'] = true;

/**
 * Access Control
 * Set a value to change, or leave empty
 */
$configs['access_control']['allow_origin'] = '*';
$configs['access_control']['allow_headers'] = '*';
$configs['access_control']['allow_methods'] = '*';
$configs['access_control']['allow_credentials'] = '*';

?>
