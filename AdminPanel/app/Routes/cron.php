<?php

$cronRoutes =  $app->group('/cron', function () use ($app) {

    $app->get('/multi-users',       'App\Controllers\Cronjob:multiUser');
    $app->get('/expire-users',      'App\Controllers\Cronjob:expireUsers');
    $app->get('/sync-traffic',      'App\Controllers\Cronjob:syncTraffic');
});

