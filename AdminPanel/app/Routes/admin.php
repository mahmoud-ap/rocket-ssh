<?php



$app->get('/login',                             'App\Controllers\Login:index');
$app->post('/ajax/login',                       'App\Controllers\Login:ajaxLogin');

$adminPanel = $app->group('', function () use ($app) {

    $app->get('/[dashboard]',                   'App\Controllers\Dashboard:index');
    $app->get('/admins',                        'App\Controllers\Admins:index');
    $app->get('/logout',                        'App\Controllers\Users:logout');


    $app->group('/users', function () use ($app) {
        $app->get('[/]',                        'App\Controllers\Users:index');
        $app->get('/online',                    'App\Controllers\Users:online');
    });

    $app->group('/settings', function () use ($app) {
        $app->get('[/]',                        'App\Controllers\Settings:index');
        $app->get('/backup',                    'App\Controllers\Settings:backup');
        $app->get('/api',                       'App\Controllers\Settings:api');
    });

    $app->group('/ajax', function () use ($app) {

        $app->group('/users', function () use ($app) {
            $app->post('',                      'App\Controllers\Users:ajaxAddUser');
            $app->post('/kill-pid',             'App\Controllers\Users:ajaxKillPidUsers');
            $app->post('/list',                 'App\Controllers\Users:ajaxUsersList');
            $app->post('/online-list',          'App\Controllers\Users:ajaxUsersOnlinesList');
            $app->put('/{id}',                  'App\Controllers\Users:ajaxEditUser');
            $app->delete('/{id}',               'App\Controllers\Users:ajaxDeleteUser');
            $app->put('/{id}/toggle-active',    'App\Controllers\Users:ajaxToggleUserActive');
            $app->put('/{id}/reset-traffic',    'App\Controllers\Users:ajaxResetUserTraffic');
        });

        $app->group('/admins', function () use ($app) {
            $app->post('',                      'App\Controllers\Admins:ajaxAddUser');
            $app->post('/list',                 'App\Controllers\Admins:ajaxUsersList');
            $app->put('/{id}',                  'App\Controllers\Admins:ajaxEditUser');
            $app->delete('/{id}',               'App\Controllers\Admins:ajaxDeleteUser');
        });

        $app->group('/settings', function () use ($app) {

            $app->post('',                      'App\Controllers\Settings:ajaxSaveSettings');
            $app->group('/public-api', function () use ($app) {
                $app->post('',                  'App\Controllers\Settings:ajaxAaddPublicApi');
                $app->post('/list',             'App\Controllers\Settings:ajaxListPublicApi');
                $app->delete('/{id}',           'App\Controllers\Settings:ajaxDeletePublicApi');
            });

            $app->group('/backup', function () use ($app) {
                $app->post('/import',           'App\Controllers\Settings:ajaxImportBackup');
                $app->post('/export',           'App\Controllers\Settings:ajaxCreateBackup');
                $app->delete('/export',         'App\Controllers\Settings:ajaxDeleteExportFile');
            });
        });
    });


    $app->group('/ajax-views', function () use ($app) {
        $app->group('/users', function () use ($app) {
            $app->get('/add',                       'App\Controllers\Users:ajaxViewAdd');
            $app->get('/{id}/edit',                 'App\Controllers\Users:ajaxViewEdit');
            $app->get('/{id}/info',                 'App\Controllers\Users:ajaxViewDetails');
        });
        $app->group('/admins', function () use ($app) {
            $app->get('/add',                       'App\Controllers\Admins:ajaxViewAdd');
            $app->get('/{id}/edit',                 'App\Controllers\Admins:ajaxViewEdit');
        });
    });
});

$adminPanel->add(new \App\Middlewares\PanelPerms($container))
    ->add(new \App\Middlewares\PanelAuth($container));
