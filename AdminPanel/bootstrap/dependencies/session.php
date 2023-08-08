<?php

if (!defined('PATH')) die();

if (getConfig('session', 'enabled')) {
  $container['session'] = function ($c) {
    return new \SlimSession\Helper;
  };
}
