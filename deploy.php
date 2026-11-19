<?php

namespace Deployer;

/**
 * Atomic Laravel deployment of a pre-built CI artifact.
 *
 * This file is run from inside the extracted build artifact (see .azure/templates/deploy.yml
 * and .github/workflows/deploy.yml). The artifact already contains production vendor/ and
 * compiled public/build assets, so neither Composer nor npm runs on the server: the release is
 * rsynced into releases/N, shared .env and storage are linked, Laravel is optimised and migrated,
 * and the "current" symlink is switched in one step.
 *
 * Required environment: DEPLOY_HOSTNAME, DEPLOY_SSH_USER, DEPLOY_PATH.
 * Optional: DEPLOY_STAGE (host alias and "stage" label, defaults to "production").
 */
require 'recipe/laravel.php';
require 'contrib/rsync.php';

/**
 * Number of releases to keep in folder
 */
set('keep_releases', 10);

/**
 * The Laravel recipe defaults writable_mode to acl, which needs setfacl on the server.
 * Uncomment if the server does not have the acl package installed.
 */
// set('writable_mode', 'skip');

/**
 * Path to php binaries
 */
// set('bin/php', '/usr/local/bin/php');

/**
 * The source directory to be rsynced: the extracted artifact this file ships in.
 */
set('rsync_src', __DIR__);

/**
 * Rsync config. The artifact is already pruned in CI; these excludes are a safety net.
 */
set('rsync', [
    'exclude' => [
        '.azure', '.editorconfig', '.env', '.env.example', '.git', '.github', '.gitignore', '.gitattributes',
        'documentation', 'node_modules', 'tests',
        '/storage', 'deploy.php',
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
        'force',
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

$stage = getenv('DEPLOY_STAGE') ?: 'production';

host($stage)
    ->set('labels', ['stage' => $stage])
    ->set('hostname', getenv('DEPLOY_HOSTNAME'))
    ->set('remote_user', getenv('DEPLOY_SSH_USER'))
    ->set('deploy_path', getenv('DEPLOY_PATH'));

/**
 * Replace "git clone" with "rsync the built artifact"
 */
task('deploy:update_code', function () {
    invoke('rsync');
});

/**
 * No Composer on the server: vendor/ is built in CI and shipped in the artifact
 */
task('deploy:vendors', function () {});

after('deploy:failed', 'deploy:unlock');
after('deploy:success', 'artisan:queue:restart');
after('rollback', 'artisan:queue:restart');
