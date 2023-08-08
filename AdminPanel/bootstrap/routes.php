<?php

if (!defined('PATH')) die();


/**
 * Load App Routes
 */
require_once(PATH_APP . DS . 'Routes' . DS . 'Routes.php');

/**
 * Add Session Middleware
 */
if (getConfig('session', 'enabled')) {
	$options = getSessionOptions();
	$app->add(new \Slim\Middleware\Session($options));
}


/**
 * Running The App
 */
$app->run();
