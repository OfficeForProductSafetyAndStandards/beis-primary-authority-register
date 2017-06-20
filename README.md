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
    
### Docker setup troubleshooting

If the installation appears to be stalled, check that you are not connected to "_The Cloud" as this blocks certain ports that are used by docker-compose.
 
# Cucumberjs/Webdriverio (e2e acceptance testing)

## How to run the test

To run your tests just call the [WDIO runner](http://webdriver.io/guide/testrunner/gettingstarted.html):

    docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio wdio.DEV.conf.js"

Environments available are: DEV

## Running single feature
Sometimes its useful to only execute a single feature file, to do so use the following command:

```sh
$ ./node_modules/.bin/wdio --spec src/features/select.feature
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
    
    
## Some useful commands

### Database client

    docker exec -it postgresql sudo -u postgres psql
    
### Raw database dump

    docker exec -i par_beta_db pg_dump -U par par > pg_dump.sql
    
### Drush dump

    docker exec -i par_beta_web  /var/www/html/vendor/bin/drush sql-dump @dev --root=/var/www/html/web --result-file=/var/www/html/docker/fresh_drupal_postgres.sql --structure-tables-key=common --skip-tables-key=common
    
### Drush import

    docker exec -i par_beta_web  /var/www/html/vendor/bin/drush sql-cli @dev --root=/var/www/html/web < fresh_drupal_postgres.sql

### Destroy containers and images and rebuild

    docker rm --force `docker ps -qa` && docker rmi $(docker images -q) && docker-compose up -d --force-recreate --build && sh setup.sh

    


