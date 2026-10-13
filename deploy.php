<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/rsync.php';

set('http_user', getenv('DEPLOY_SSH_USER'));
set('bin/php', '/usr/local/bin/php');
set('allow_anonymous_stats', false);

// what gets sent: the built tree on the runner
set('rsync_src', __DIR__);
set('rsync', [
    'exclude' => [
        '.git', '.github', 'node_modules', 'tests',
        '.env', '/storage', 'deploy.php',
    ],
    'flags' => 'rz',
    'options' => ['delete'],
    'timeout' => 300,
]);

host('production')
    ->set('labels', ['stage' => 'production'])
    ->set('hostname', getenv('DEPLOY_HOSTNAME'))
    ->set('remote_user', getenv('DEPLOY_SSH_USER'))
    ->set('deploy_path', getenv('DEPLOY_PATH'));

// replace "git clone" with "rsync the built artefact"
task('deploy:update_code', ['rsync']);

// no composer on the server
task('deploy:vendors', function () {});

after('deploy:failed', 'deploy:unlock');
after('deploy:success', 'artisan:queue:restart');
after('rollback', 'artisan:queue:restart');
