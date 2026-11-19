# Azure DevOps staging pipeline

What happens from a push to a live release, using `.azure/workflows/staging.yml`, `.azure/templates/build.yml`, `.azure/templates/deploy.yml` and `deploy.php`.

## 1. Trigger

A push to `develop` starts `.azure/workflows/staging.yml` on the self-hosted `Default` agent pool. It loads the `laravel-lunar-staging` variable group and runs two stages in order: **build**, then **deploy**. Deploy only runs if build succeeded.

## 2. Build stage (`.azure/templates/build.yml`)

This all happens in the agent's checkout of the repo.

1. **Toolchain.** Switches the agent's PHP to 8.5 and selects Node 22.
2. **Restore caches.** Composer and npm download caches are restored, keyed on `composer.lock` and `package-lock.json`. If a lock file hasn't changed, installs come from the cache.
3. **Install dev dependencies.** Runs `composer install` (including Pest, Pint and so on), then `npm ci` and `npm run build`, which writes the compiled assets to `public/build`.
4. **Test.**
   - Creates a throwaway `.env` from `.env.example` and generates an app key.
   - Creates an empty `testing` database on `CI_DB_HOST`.
   - Runs `php artisan test --compact`, pointed at MariaDB through environment variables. The tests migrate and seed the database themselves (`LazilyRefreshDatabase` with Lunar's `TestingSeeder`).
   - Drops the database, even if the tests failed. If any test fails, the run stops here and nothing is deployed.
5. **Optimise for production.**
   - Runs `composer install --no-dev --optimize-autoloader --classmap-authoritative`, which removes dev packages from `vendor/` and builds a fast autoloader.
   - Deletes the test `.env` and `bootstrap/cache/*.php`, so no test settings are baked into the release.
6. **Package.**
   - Copies the project into a staging folder, leaving out what production doesn't need: `node_modules`, `tests`, `.git`, `documentation`, `docker`, editor and AI tool folders, `.env*`, `storage`, and so on.
   - `deploy.php` stays in, because the next stage runs it.
   - Tars the folder into `laravel-release.tar.gz` and publishes it as the pipeline artifact.

What gets shipped: application code, production `vendor/`, compiled `public/build`, and `deploy.php`.

## 3. Deploy stage (`.azure/templates/deploy.yml`)

This is a deployment job on the `staging` environment, so it records history and waits at any approval check you add.

1. **Download.** Fetches `laravel-release.tar.gz` from this run and extracts it to `$(Pipeline.Workspace)/release`.
2. **SSH key.** `InstallSSHKey@0` loads the `staging_deploy_key` secure file into the agent's ssh-agent and trusts the server using `SSH_KNOWN_HOSTS`. Nothing touches the server yet.
3. **Deployer.** Downloads `deployer.phar` v8.0.5. The `dep` command in `vendor/bin` was removed with the dev dependencies, so it can't come from there.
4. **Run.** Inside the extracted folder, runs `php dep.phar deploy stage=staging`, with `DEPLOY_HOSTNAME`, `DEPLOY_SSH_USER` and `DEPLOY_PATH` set.

## 4. On the server (`deploy.php` and the Laravel recipe)

Deployer SSHes in as `DEPLOY_SSH_USER`. The host is named `staging` because `DEPLOY_STAGE=staging`. The deploy folder looks like this:

```
DEPLOY_PATH/
├── current -> releases/42      (what the web server serves)
├── releases/
│   ├── 41/
│   └── 42/
└── shared/
    ├── .env                    (created once, by hand)
    └── storage/
```

1. **Prepare.** Creates the folders on the first run and locks the deploy so two can't run at once. Then makes a new `releases/N`.
2. **Upload.** Instead of a git clone, rsyncs the extracted artifact from the agent into `releases/N`, which is the overridden `deploy:update_code` task.
3. **Link shared files.** Symlinks `shared/.env` and `shared/storage` into the release and makes the storage and cache folders writable.
4. **Vendors.** Does nothing, because `deploy:vendors` is a no-op. Composer and npm never run on the server.
5. **Artisan, still on the new release folder.** The site keeps serving the old release during these.
   - `php artisan storage:link`
   - `php artisan optimize` rebuilds the config, route, event and view caches from the real `.env`.
   - `php artisan migrate --force` runs migrations. It's skipped without an error if `shared/.env` is missing or empty.
6. **Publish.** Switches the `current` link to `releases/N` in one step, which is the atomic part. Then unlocks the deploy and removes releases beyond the newest 10.
7. **After publish.**
   - `php artisan queue:restart` makes workers pick up the new code.
   - `php artisan reload` reloads running services (Laravel 12.41 or later).

The deploy doesn't run anything specific to Lunar or Scout, such as rebuilding the Meilisearch index. Add a task hooked after `artisan:migrate` in `deploy.php` if you need one.

## 5. If something fails

- **Build fails** (for example a test): there's no artifact, the deploy stage doesn't run, and the server is untouched.
- **Deploy fails before the switch** (rsync, `optimize`, migration): `current` still points at the old release and the lock is released. A half-run migration is not undone, though.
- **Bad release after it's live:** run `dep rollback stage=staging` with the same environment variables. It points `current` at the previous release and restarts the queue.

## 6. Production

`.github/workflows/deploy.yml` still deploys production on pushes to `main`. It uses the same `deploy.php`, and with `DEPLOY_STAGE` unset the host defaults to `production`.

## One-time setup

### Azure DevOps

- Secure file `staging_deploy_key`: the private deploy key.
- Environment `staging`.
- Variable group `laravel-lunar-staging`:
  - `DEPLOY_HOSTNAME`
  - `DEPLOY_SSH_USER`
  - `DEPLOY_PATH`
  - `SSH_KNOWN_HOSTS` (the output of `ssh-keyscan <host>`)
  - `CI_DB_HOST`
  - `CI_DB_PASSWORD` (mark it secret)

### Pipeline agent

- PHP 8.5, `rsync`, `ssh`, `curl` and the `mariadb` client.
- A MariaDB server reachable at `CI_DB_HOST` for the tests.

### Server

- `DEPLOY_PATH/shared/.env` with the real staging settings.
- PHP 8.5 and `rsync`.
- Either `setfacl` installed (the `acl` package), or uncomment `set('writable_mode', 'skip')` in `deploy.php`.
