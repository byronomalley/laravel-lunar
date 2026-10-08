<?php

namespace Deployer;

// load in the Laravel recipe, this will do the heavy lifting.
require 'recipe/laravel.php';

// tell Deployer where your Git repository is
set('repository', getenv('DEPLOY_REPO'));

// the http user, generally the same as the SSH/remote_user
set('http_user', getenv('DEPLOY_SSH_USER'));

// configure your environments, you can have as many as you like here!
host('production')
    ->set('labels', ['stage' => 'production'])
    ->set('hostname', getenv('DEPLOY_HOSTNAME')) // the server hostname: ip address
    ->set('branch', getenv('DEPLOY_BRANCH')) // the git branch to deploy: 'release/production'
    ->set('remote_user', getenv('DEPLOY_SSH_USER')) // the SSH user: 'username'
    ->set('deploy_path', getenv('DEPLOY_PATH')); // the path to deploy to: '/home/username/htdocs/production'


// its likely that you can get away without modifying anything more
// and you'd have a successful deployment at this point.

// define the paths to PHP & Composer binaries on the server
set('bin/php', '/usr/local/bin/php');
set('bin/npm', '/usr/local/bin/npm');
set('bin/composer', '{{bin/php}} /usr/local/bin/composer');

// a couple of additional options
set('allow_anonymous_stats', false);
set('git_tty', true);

// now onto the build steps, in most cases, you can leave these as below,
// but you can add or remove build steps as required!

// compile our production assets
task('npm:build', function () {
    run('cd {{release_path}} && {{bin/npm}} install');
    run('cd {{release_path}} && {{bin/npm}} run build');
    run('cd {{release_path}} && {{bin/npm}} install --omit=dev');
})->desc('Compile npm files locally');
after('deploy:vendors', 'npm:build');

// automatically unlock when a deploy fails
after('deploy:failed', 'deploy:unlock');

// after a deploy, clear our cache and run optimisations
after('deploy:cleanup', 'artisan:cache:clear');
after('deploy:cleanup', 'artisan:optimize');

// handle queue restarts
after('deploy:success', 'artisan:queue:restart');
after('rollback', 'artisan:queue:restart');
