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

### Definition of ready

* Story A/Cs have been discussed with QA, and PO where applicable
* Story has been refined, and sized
* There is enough information on the ticket to complete it
* Any UI changes have been prototyped and agreed by stakeholders

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
Deployments are handled through CircleCI. All started jobs can be found on [CircleCI](https://app.circleci.com/pipelines/github/UKGovernmentBEIS/beis-primary-authority-register).

Locate the tag you've just created under the "Branch / Commit" column:![image](https://user-images.githubusercontent.com/334114/230381309-a5b8a11e-5b27-4499-9db2-ae76757472b6.png)

And all tagged deployments will have to be manually released through CI once they have passed manual regression testing. They will then be released to staging and production, or to the relevant test environment.

#### Production

Tagging the **master branch** with a **semver tag** starting with a lowercase `v` e.g. `v1.0.0` will start a production deployment build.

```
git tag v0.0.31
git push --tags
```
The build will be deployed first to [Staging](https://beis-par-staging.cloudapps.digital), and then to [Production](https://primary-authority.beis.gov.uk/).

#### Non-Production / Test
Tagging **any branch** with **any other tag** will start a test deployment and allow the feature to be deployed to a test environment.

The tag name will be split on the `-` character, and the first part will be used as the name of the environment. e.g. `test-some_feature-01` will deploy to the `test` environment.

The build will be deployed to a non-production environment - https://beis-par-{ENVIRONMENT}.cloudapps.digital - e.g. https://beis-par-test.cloudapps.digital

## Development environment

Docker can be used as the local development environment.

There is a docker-compose file in the project root which contains all the images needed to run PAR, these are the same containers that are run in CI:
* Web (primary container)
* Postgres
* Opensearch

There are various local build stack options available depending on developer preference, the default is the custom docker-compose approach, however there are options for ARM64, Ddev and Docksal users (see further down this file)

Just run `docker-compose up -d` from the project root.

To run commands within the primary container:
```
docker exec -it beis-par-web /bin/bash
$ cd /var/www/html/web
$ ../vendor/bin/drush cr
```

Once you have a working development environment PAR should be available at [https://par.localhost:8080](https://par.localhost:8080)

### Prerequisites

* [git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git) - version 2.0 or higher
* [php](https://www.php.net/) - version 8.1 or higher
* [Composer](https://getcomposer.org/download) - version 2.3.5 or higher
* [Docker](https://docs.docker.com/engine/install) - version 20.0 or higher
* [Docker Compose](https://docs.docker.com/compose/install) - version 2.2.2 or higher
* (Optional) A copy of the [latest sanitised PAR database](https://s3.eu-west-2.amazonaws.com/beis-par-artifacts/backups/drush-dump-production-sanitized-latest.sql.tar.gz) from the BEIS S3 artifacts bucket.
* A copy of the [settings.local.php](https://beis-par-artifacts.s3.eu-west-2.amazonaws.com/dev/settings.local.php) configuration file required to setup the application locally.

#### Additional Windows prerequisites
If on windows follow the instructions below, essentially docker performance on windows is poor and needs to run from the WSL distro (use the latest Ubuntu LTS).

* [Install WSL2](https://learn.microsoft.com/en-us/windows/wsl/install)
  * [Configure .wslconfig](https://learn.microsoft.com/en-us/windows/wsl/wsl-config#configuration-setting-for-wslconfig), and set maximum memory and processor limits
* [Install Docker Desktop & enable WSL 2 support](https://docs.docker.com/desktop/windows/wsl/#install)
* [Clone the repository into the WSL 2 distro's filesystem](https://docs.docker.com/desktop/windows/wsl/#best-practices), for better performance
  * This can be found by navigating to `\\wsl$\{DISTRO}\home` in the file explorer, where `{DISTRO}` can be found by running `wsl -l`, e.g. `Ubuntu-22.04`
* php, composer & patch (available through `C:/Program Files/Git/usr/bin`) need to be added to the system path

### Mac M1 users
Mac users on ARM64 devices will need to copy the docker-compose.overrides.yml.example to docker-compose.overrides.yml, this swaps the precompiled images to latest docker images for Postgres
```
cp docker-compose.override.yml.example docker-compose.override.yml
docker-compose up -d
```

### Ddev users
If ddev is your local setup of choice, there are a couple of useful tasks in the scripts/ddev directory.

To set up / reinstall on ddev run, you will need to have an up-to-date DB in the /backups directory.
NB. The solr.server and system.performance settings
```
./scripts/ddev/install.sh
```

If you wish to update an existing local site without a db import
```
./scripts/ddev/update.sh
```

### Docksal users
If docksal is your local setup of choice, there are a couple of useful tasks in the scripts/docksal directory.

To set up / reinstall on ddev run, you will need to have an up-to-date DB in the /backups directory.
NB. The solr.server and system.performance settings

Ensure to empty the db connection settings from lines 1170 - 1180 pf settings.php first
```
./scripts/docksal/install.sh
```
If you wish to update an existing local site without a db import
```
./scripts/ddev/update.sh
```

### Set up

There are a few main tasks that need to be performed after pulling new code or downloading a new database.

#### Composer install
After pulling any changes to the `composer.json` file, run:

```
composer install
```

This is best run from outside the primary docker container (very slow within the container), on your local machine.

#### NPM install
The theme and the tests dependencies are both managed with NPM, any changes to `package.json` or `tests/package.json`, run:

```
npm install
npm run install-govuk-theme
npm run install-par-theme
npm run gulp
```

#### Database (optional)

The docker container includes a seed database that can be used to get started.

In order to get the latest and most up-to-date database including some of the par data you will need access to AWS:
```
aws s3 cp s3://beis-par-artifacts/backups/db-dump-production-{DB_TYPE}-latest.tar.gz ./backups/
```
Where `{DB_TYPE}` is one of:

* seed
* sanitised

Download this and place in the `backups` directory of the par project (create the folder if it doesn't' exist).

##### Extract the database
```
cd ./backups
tar -zxvf ../backups/db-dump-production-{DB_TYPE}-latest.tar.gz db-dump-production-{DB_TYPE}.sql
```
**Note:** On some systems, such as windows, and for some files the downloaded archives are not compressed and may end in `.tar` instead of `.tar.gz`

##### Import the database using drush

You will need the `settings.local.php` before you run this, see the Drupal install section below.

```
cd ./web
../vendor/bin/drush sql:drop
../vendor/bin/drush sql:cli < ../backups/db-dump-production-{DB_TYPE}.sql
```

#### Drupal install
Get a copy of the settings.local.php file that will configure Drupal within your local environment and place this in `web/sites/default`:
```
aws s3 cp s3://beis-par-artifacts/dev/settings.local.php ./web/sites/default/settings.local.php
```

To set-up drupal, on whenever switching branches or importing a fresh database, run:

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

To debug Drush commands you will need to set two environment variables in the running web container.
```
export XDEBUG_CONFIG=idekey=PHPSTORM
export PHP_IDE_CONFIG=servername=par.localhost
```
To stop Drush debugging unset the variables.
```
unset XDEBUG_CONFIG
unset PHP_IDE_CONFIG
```

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
