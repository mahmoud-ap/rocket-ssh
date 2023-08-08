<?php

if (!defined('PATH')) die();

/**
 * Session
 * to deisable: empty session name
 */
// enabled: bool
$configs['session']['enabled'] = true;
// name: string
$configs['session']['name'] = 'sv-panel';
// autorefresh: bool
// true if you want session to be refresh when user activity is made
// activity: (interaction with server)
$configs['session']['autorefresh'] = false;
// lifetime: string
// How much should the session last? Default 20 minutes.
// Any argument that strtotime can parse is valid.
$configs['session']['lifetime'] = '1 day';
// path: string
// slash or a valid path
$configs['session']['path'] = '/';
// domain: string
// null or a valid domain name
$configs['session']['domain'] = null;
// secure: bool
$configs['session']['secure'] = false;
// httponly: bool
$configs['session']['httponly'] = false;
// ini_settings: array
// https://www.php.net/manual/en/session.configuration.php
// be carefull, danger of performance leaks 
$configs['session']['ini_settings'] = [];

?>
