<?php 

if (!defined('PATH')) die();

/**
 * SLIM 3
 */

// Turn this on in development mode to get information about errors
$configs['displayErrorDetails'] = true;
// Allows the web server to set the Content-Length header
// which makes Slim behave more predictably.
$configs['addContentLengthHeader'] = false;
// When true, the route is calculated before any middleware is executed.
// This means that you can inspect route parameters in middleware if you need to.
$configs['determineRouteBeforeAppMiddleware'] = false;

/**
 * Slim PHP View
 */

// enabling or disabling php view di, bool
$configs['view']['enabled'] = true;
// default meta title
$configs['view']['metaTitle'] = 'Xpanel';
// default meta description
$configs['view']['metaDesc'] = 'Xpanel framework';
