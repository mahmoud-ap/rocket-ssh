<?php

if (!defined('PATH')) die();

if (getConfig('db', 'enabled')) {
    /*
    * Service factory for the ORM
    */
    $container['db'] = function ($c) {
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($c['settings']['db']);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $container      = new Illuminate\Container\Container;
        $connFactory    = new \Illuminate\Database\Connectors\ConnectionFactory($container);

        $conn           = $connFactory->make($c['settings']['db']);

        $resolver       = new \Illuminate\Database\ConnectionResolver();
        $resolver->addConnection('default', $conn);
        $resolver->setDefaultConnection('default');
        \Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);


        return $capsule;
    };
}
