<?php

if (!defined('PATH')) die();

use Slim\Views\PhpRenderer;

if (getConfig('view', 'enabled')) {
    $container['view'] = function ($container) {
        
        $templateVars = [];
        $viewsPath = PATH_APP . DS . 'Views' . DS;
        return new PhpRenderer($viewsPath, $templateVars);
    };
}
