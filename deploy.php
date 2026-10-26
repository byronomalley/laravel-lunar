<?php

namespace Deployer;

/**
 * Load the official Laravel recipe
 */
require 'recipe/laravel.php';

/**
 * Load the rsync recipe
 */
require 'contrib/rsync.php';

/**
 * Name of current user who is running deploy. If not set will try automatically get git user name, otherwise output of whoami command.
 */
set('user', getenv('DEPLOY_SSH_USER'));


/**
 * Number of releases to keep in folder
 */
set('keep_releases', 10);


/**
 * Skip writable mode, because
 *
 * The Laravel recipe defaults writable_mode to acl, and your server doesn’t have setfacl.
 * You could install it (apt-get install acl), but that needs sudo, and you don’t need ACLs for this setup.
 *
 * Install setfacl?
 */
//set('writable_mode', 'skip');

/**
 * Path to php binaries
 */
//set('bin/php', '/usr/local/bin/php');
// set('bin/php', 'docker compose -f {{deploy_path}}/docker-compose.yml exec -T -u www-data -w {{release_or_current_path}} {{docker_service}} php');

/**
 * The source directory to be rsynced
 */
set('rsync_src', __DIR__);

/**
 * Rsync config
 */
set('rsync', [
    'exclude' => [
        '.azure', '.editorconfig', '.env.example', '.git', '.gitignore', '.gitattributes',
        'documentation',
        '.git', '.github', 'node_modules', 'tests',
        '.env', '/storage', 'deploy.php',
    ],
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'rz',
    'options' => [
        'delete',
        'delete-after',
        'force'
    ],
    'timeout' => 300,
]);

add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

// replace "git clone" with "rsync the built artefact"
task('deploy:update_code', function () {
    invoke('rsync');
});

host('production')
    ->set('labels', ['stage' => 'production'])
    ->set('hostname', getenv('DEPLOY_HOSTNAME'))
    ->set('remote_user', getenv('DEPLOY_SSH_USER'))
    ->set('deploy_path', getenv('DEPLOY_PATH'));

// Replace "git clone" with "rsync the built artefact"
task('deploy:update_code', function () {
    invoke('rsync');
});

// No composer on the server: vendor/ is built in CI and shipped
task('deploy:vendors', function () {});

after('deploy:failed', 'deploy:unlock');
after('deploy:success', 'artisan:queue:restart');
after('rollback', 'artisan:queue:restart');
