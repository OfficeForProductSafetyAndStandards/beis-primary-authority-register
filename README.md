# Department of Business Energy and Industrial Strategy - Regulatory Authority

# Primary Authority Register

[![Build Status](https://travis-ci.org/UKGovernmentBEIS/beis-primary-authority-register.svg?branch=master)](https://travis-ci.org/UKGovernmentBEIS/beis-primary-authority-register)

## Web Application

Please see the [web application readme file](https://github.com/UKGovernmentBEIS/beis-primary-authority-register/blob/master/web/README.md) in the web directory for more information about Drupal and how to configure the web application.

## Ways of Working

There are some basic ways of working with the PAR project to ensure consistency and code quality across the team.

* We use feature branching and merge all code back into the master branch via PRs.
Feature branches should preferably be named after the task number.

* We request peer reviews from other developers against all completed tasks.
PRs should be tested against the Definition of Done.

* We deploy through a release strategy by tagging the master branch.

### Definition of Done

* Code meets the A/C agreed on the task.
* TTD requirements met. New tests written for all A/C (at a minimum).
* Coding standards met. `drupal-check -d` is essential, `phpcs` is optional:
```
./vendor/bin/drupal-check -d --memory-limit=256M web/modules/custom/ web/modules/features/ web/themes/custom/
./vendor/bin/phpcs web/modules/custom/ web/modules/features/ web/themes/custom/
```
* Can be deployed to an existing environment without manual intervention.
* No regressions found from local testing.

### Deployment

Tagging the master branch with a semver tag starting with a lowercase `v` will start a deployment build.

```
git tag v0.0.31
git push --tags
```

Tagging any branch with any other tag will start a test deployment and allow the feature to be deployed to a test environment.

All started jobs can be found on [CircleCI](https://app.circleci.com/pipelines/github/UKGovernmentBEIS/beis-primary-authority-register). And all tagged deployments will have to be manually released through CI.

## Development environment

Docker can be used as the local development environment.

There is a docker-compose file in the project root which contains all the images needed to run PAR, these are the same containers that are run in CI:
* Web (primary container)
* Postgres
* Opensearch

Just run `docker-compose up -d` from the project root.

To run commands within the primary container:
```
docker exec -it beis-par-web /bin/bash
$ cd /var/www/html/web
$ ../vendor/bin/drush cr
```

Once you have a working development environment PAR should be available at [https://par.localhost:8080](https://par.localhost:8080)

### Prerequisites

* [Composer] - version 2.3.5 or higher
* [Docker] - version 20.0 or higher
* [Docker Compose] - version 2.2.2 or higher
* A copy of the [latest sanitised PAR database](https://s3.eu-west-2.amazonaws.com/beis-par-artifacts/backups/drush-dump-production-sanitized-latest.sql.tar.gz) from the BEIS S3 artifacts bucket.

### Set up

There are a few main tasks that need to be performed after pulling new code or downloading a new database.

#### Composer install
After pulling any changes to the `composer.json` file, run:

```
composer install
```

This is best run from outside the primary docker container (very slow within the container), on your local machine.

#### Database

In order to run the site you will need to import a copy of the latest par database:
```
aws s3 cp s3://beis-par-artifacts/backups/db-dump-production-unsanitised-latest.tar.gz ./backups/
```
Download this and place in the `backups` directory of the par project (create the folder if it doesn't' exist).

Import the database using drush (note the database should be truncated before re-importing)
```bash
cd /var/www/par/backups
tar -zxvf ../backups/db-dump-production-sanitised-latest.tar.gz db-dump-production-sanitised.sql
cd /var/www/par/web
../vendor/bin/drush sql:cli < ../backups/db-dump-production-sanitised.sql
```

#### NPM install
The theme and the tests dependencies are both managed with NPM, any changes to `package.json` or `tests/package.json`, run:

```
npm install
npm run install-govuk-theme
npm run install-par-theme
npm run gulp
```

#### Drupal install
After a fresh database import, or when switching branches always re-install drupal, run:

```
./drupal-update.sh
```

#### Debugging

The Xdebug PHP extension is included in the web container image. It is disabled by default.

To activate the debugger set the XDEBUG environment variable to 'debug' before starting the services.

```
export XDEBUG=debug
docker-compose up -d web
```

To deactivate Xdebug remove the XDEBUG environment variable, or set to 'off', and restart.

```
export XDEBUG=off
docker-compose up -d web
```

To avoid slowing execution when debugging is not required Xdebug is configured for debugging
to start only when triggered, it will not initiate a connection to the IDE unless a trigger is
present. Which trigger to use depends on whether you're debugging a PHP application through
a browser, or on the command line. See [Xdebug activating step debugging](https://xdebug.org/docs/step_debug#activate_debugger)
for more information about triggering debugging.

## Backup database

The build relies on a seed database which is a sanitised version of the production database. At regular periods this seed database needs to be updated.

Typically this process will be handled by a daily CI job, with database backups being stored to the S3 bucket `beis-par-artifacts` with the prefix `backups/`.

But should this process need to be run manually...

#### Backup the production database
```
cf ssh beis-par-production -c "python app/devops/tools/postgres_dump.py"
```

#### Sanitize the production database
Go to the S3 artifacts bucket and download a copy of the drush-dump-production-unsanitized.sql file that was just created (and uploaded).

**NOTE:** You must always sanitize the database against the latest release code _only_ with dev and test settings files turned off:
```
$config['config_split.config_split.dev_config']['status'] = FALSE;
$config['config_split.config_split.test_config']['status'] = FALSE;
```

Import the newly downloaded production db:
```
cd ./web
tar -zxvf ./location/of/downloaded/sql/drush-dump-production-unsanitized-latest.sql.tar.gz
../vendor/bin/drush @par.paas sql:drop -y
../vendor/bin/drush @par.paas sql:cli < ./location/of/downloaded/sql/drush-dump-production-unsanitized-latest.sql
cd ..
./drupal-update.sh
```

Check that the data looks right and then sanitise:
```
cd ./web
../vendor/bin/drush @par.paas sql:sanitize -y
```

Then dump the db, zip it and upload it back to the S3 artifacts bucket with the correct name:
```../vendor/bin/drush @par.paas sql-dump --result-file=./drush-dump-production-sanitized-latest.sql --extra="-O -x"
tar -zcvf drush-dump-production-sanitized-latest.sql.tar.gz -C ./ drush-dump-production-sanitized-latest.sql
../vendor/bin/drush fsp s3backups drush-dump-production-sanitized-latest.sql.tar.gz drush-dump-production-sanitized-latest.sql.tar.gz```
