DEPLOY_HOSTNAME
DEPLOY_SSH_USER
DEPLOY_PATH
PRIVATE_KEY

```yml
name: deploy

on:
  push:
    branches: [main]

concurrency: production_environment

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
      - uses: shivammathur/setup-php@v2
        with:
          php-version: "8.5"
          tools: composer

      - uses: actions/setup-node@v4
        with:
          node-version: 22
          cache: npm

      - run: composer install --no-interaction --prefer-dist
      - run: npm ci && npm run build
      - run: php artisan test

      # production-only vendor dir is what gets shipped
      - run: composer install --no-dev --optimize-autoloader --no-interaction

      - name: Deploy
        uses: deployphp/action@v1
        env:
          DEPLOY_HOSTNAME: ${{ secrets.DEPLOY_HOSTNAME }}
          DEPLOY_SSH_USER: ${{ secrets.DEPLOY_SSH_USER }}
          DEPLOY_PATH: ${{ secrets.DEPLOY_PATH }}
        with:
          private-key: ${{ secrets.PRIVATE_KEY }}
          dep: deploy
```

Deployer script

```php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/rsync.php';

// the http user, generally the same as the SSH/remote_user
set('http_user', getenv('DEPLOY_SSH_USER'));

// define the paths to PHP & Composer binaries on the server
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

// automatically unlock when a deploy fails
after('deploy:failed', 'deploy:unlock');

// after a deploy, clear our cache and run optimisations
after('deploy:cleanup', 'artisan:cache:clear');
after('deploy:cleanup', 'artisan:optimize');

// handle queue restarts
after('deploy:success', 'artisan:queue:restart');
after('rollback', 'artisan:queue:restart');
```
