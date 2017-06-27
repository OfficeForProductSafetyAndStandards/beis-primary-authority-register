#!/usr/bin/env bash

PRECOMMAND=""

case "$(uname -s)" in

   Darwin)
     echo 'Mac OS X'
     ;;

   Linux)
     echo 'Linux'
     ;;

   CYGWIN*|MINGW32*|MINGW64*|MSYS*)
     echo 'MS Windows'
     dos2unix ../drupal-update.sh
     PRECOMMAND="winpty"
     ;;

   *)
     echo 'other OS' 
     ;;
esac

# Pull the docker images

    docker-compose up -d --force-recreate

# Install dependencies

    $PRECOMMAND docker exec -ti par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
    cat ../web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
fi
    
# Install test dependencies
 
    $PRECOMMAND docker exec -it par_beta_web bash -c "cd /var/www/html/tests && rm -rf node_modules && ../../../../usr/local/n/versions/node/7.2.1/bin/npm install"

# Load the test data:
 
    sleep 5 # Time for the server to boot
    $PRECOMMAND docker exec -it par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < docker/fresh_drupal_postgres.sql"

# Update Drupal

    $PRECOMMAND docker exec -it par_beta_web bash -c "sh drupal-update.sh /var/www/html"

# Install front end dependencies

    $PRECOMMAND docker exec -it par_beta_web bash -c "rm -rf node_modules && ../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    $PRECOMMAND docker exec -it par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm run gulp"




