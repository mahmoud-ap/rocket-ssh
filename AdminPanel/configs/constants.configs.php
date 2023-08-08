<?php 

if (!defined('PATH')) die();

/**
 * General
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * PATHs
 * Main
 */
defined('PATH_APP')     || define('PATH_APP', PATH . DS . 'app');
defined('PATH_ASSETS')  || define('PATH_ASSETS', PATH . DS . 'assets');
defined('PATH_CONFIGS') || define('PATH_CONFIGS', PATH . DS . 'configs');
defined('PATH_STORAGE') || define('PATH_STORAGE', PATH . DS . 'storage');
defined('PATH_BOOTSTRAP')  || define('PATH_BOOTSTRAP', PATH . DS . 'bootstrap');
defined('PATH_TESTS')   || define('PATH_TESTS', PATH . DS . 'tests');

/**
 * PATHs
 * Storage Subs
 */
defined('PATH_CACHE')    || define('PATH_CACHE', PATH_STORAGE . DS . 'cache');
defined('PATH_DEBUG')    || define('PATH_DEBUG', PATH_STORAGE . DS . 'debug');
defined('PATH_LOGS')     || define('PATH_LOGS', PATH_STORAGE . DS . 'logs');
defined('PATH_SESSIONS') || define('PATH_SESSIONS', PATH_STORAGE . DS . 'sessions');
defined('PATH_TEMP')     || define('PATH_TEMP', PATH_STORAGE . DS . 'temp');

/**
 * Timing Constants
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2592000);
defined('YEAR')   || define('YEAR', 31536000);
defined('DECADE') || define('DECADE', 315360000);

?>
