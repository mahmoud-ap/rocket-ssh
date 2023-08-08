<?php

if (!defined('PATH')) die();


/** Load Dotenv */
$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(PATH);
$dotenv->load();


/**
 * Loading config files
 */
foreach (glob('configs/*.configs.php') as $filename) {
  require_once($filename);
}

/**
 * Loading Core Funcions
 */
require_once('core.php');

/**
 * Init Actions
 */
setTimeZone(getConfig('timezone'));
setPhpErrorReporting(getConfig('show_errors'));
setAccessControl(getConfig('access_control'));

/**
 * Starting Slim App with the configs
 */


$app = new Slim\App(['settings' => $configs]);

/**
 * Dependencies Container
 */
$container = $app->getContainer();

/**
 * Injecting Dependencies
 */
require_once('dependencies.php');

/**
 * Loading routes
 */
require_once 'routes.php';
