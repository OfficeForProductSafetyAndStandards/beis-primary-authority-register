## Docker Development Environment

Install [docker](https://docs.docker.com/engine/installation/linux/ubuntu/) (or [Docker for Windows](https://docs.docker.com/docker-for-windows/install/), or [Docker for Mac](https://docs.docker.com/docker-for-mac/install/)), then:

    cd docker
    sh setup.sh
    
Request the hash salt from another member of the team and add this to the hash setting at the bottom of your local settings file:

    vi ../web/sites/default/settings.local.php
    
You can then visit the site at:

    http://127.0.0.1:8111
    
### Docker setup troubleshooting

If the installation appears to be stalled, check that you are not connected to a public WiFi such as "_The Cloud" as these often block certain ports that are used by docker-compose.
 
# Cucumberjs/Webdriverio (e2e acceptance testing)

## How to run the test

To run your tests just call the [WDIO runner](http://webdriver.io/guide/testrunner/gettingstarted.html):

    docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio wdio.BUILD.conf.js"

Environments available are: DEV (Chrome), BUILD (PhantonJS). DEV won't work when running the application using the Docker containers.

## Running single feature
Sometimes its useful to only execute a single feature file, to do so use the following command:

    docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio --spec src/features/beis.feature wdio.BUILD.conf.js"
        
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

    



