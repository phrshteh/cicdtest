<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/rsync.php';

set('application', 'My App');
set('ssh_multiplexing', true);

set('rsync_src', function () {
    return __DIR__;
});


add('rsync', [
    'exclude' => [
        '.git',
        '/.env',
        '/storage/',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
    ],
]);

task('deploy:secrets', function () {
    file_put_contents(__DIR__.'/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path').'/shared');
});

host('myapp.io')
    ->hostname('213.108.241.201')
    ->stage('production')
    ->user('shakiba')
    ->set('deploy_path', '/var/www/html/cicdtest');

//host('staging.myapp.io')
//    ->hostname('213.108.241.201')
//    ->stage('staging')
//    ->user('shakiba')
//    ->set('deploy_path', '/var/www/my-app-staging');

after('deploy:failed', 'deploy:unlock');

desc('Deploy the application');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:secrets',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'artisan:queue:restart',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);