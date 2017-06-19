# beis-par-beta

[![Build Status](https://travis-ci.org/TransformCore/beis-par-beta.svg?branch=master)](https://travis-ci.org/TransformCore/beis-par-beta)

Herein lies the fruits of our endevour to create a world class digital service for Regulatory Authority.

## Docker development environment

Install [docker](https://docs.docker.com/engine/installation/linux/ubuntu/) (or [Docker for Windows](https://docs.docker.com/docker-for-windows/install/), or [Docker for Mac](https://docs.docker.com/docker-for-mac/install/)), then:

    cd docker
    docker-compose up -d --force-recreate --build && sh setup.sh
	
Request the hash salt from another member of the team and add this to the hash setting at the bottom of your local settings file:

    vi ../sites/default/settings.local.php
    
You can then visit the site at:

    http://127.0.0.1:8111
    
## Some useful commands

### Database client

    docker exec -it postgresql sudo -u postgres psql
    
### Raw database dump

    docker exec -i pars_beta_db pg_dump -U pars pars > pg_dump.sql
    
### Drush dump

    docker exec -i pars_beta_web  /var/www/html/vendor/bin/drush sql-dump @dev --root=/var/www/html/web --result-file=/var/www/html/docker/fresh_drupal_postgres.sql --structure-tables-key=common --skip-tables-key=common
    
### Drush import

    docker exec -i pars_beta_web  /var/www/html/vendor/bin/drush sql-cli @dev --root=/var/www/html/web < fresh_drupal_postgres.sql

### Destroy containers

    docker rm pars_beta_web pars_beta_db --force

## Drupal site setup

To download all dependencies:

    sh composer.sh install
    
The to configure the application:

    docker exec -i pars_beta_web sh /var/www/html/docker/drupal-update.sh


# Cucumberjs/Webdriverio (e2e acceptance testing)

* Install the dependencies (`npm install` or `yarn install`)

## How to run the test

To run your tests just call the [WDIO runner](http://webdriver.io/guide/testrunner/gettingstarted.html):

```sh
$ ./node_modules/.bin/wdio wdio.<ENVIRONMENT>.conf.js
```

Environments available are: DEV

## Running single feature
Sometimes its useful to only execute a single feature file, to do so use the following command:

```sh
$ ./node_modules/.bin/wdio --spec ./test/features/select.feature
```

## Using tags

If you want to run only specific tests you can mark your features with tags. These tags will be placed before each feature like so:

```gherkin
@Tag
Feature: ...
```

To run only the tests with specific tag(s) use the `--tags=` parameter like so:

```sh
$ ./node_modules/.bin/wdio --tags=@Tag,@AnotherTag
```

You can add multiple tags separated by a comma

## Pending test

If you have failing or unimplemented tests you can mark them as "Pending" so they will get skipped.

```gherkin
// skip whole feature file
@Pending
Feature: ...

// only skip a single scenario
@Pending
Scenario: ...
```