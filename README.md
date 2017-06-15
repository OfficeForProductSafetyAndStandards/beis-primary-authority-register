# beis-par-beta

Herein lies the fruits of our endevour to create a world class digital service for Regulatory Authority.

## Docker development environment

Install docker (or Docker for Windows, or Docker for Mac), then:

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




