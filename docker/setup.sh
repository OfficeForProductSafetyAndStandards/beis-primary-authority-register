#!/usr/bin/env bash

# Install dependencies

    docker exec -i par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
    cat ../web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
fi

# Install test dependencies

    docker exec -i par_beta_web bash -c "cd /var/www/html/tests && rm -rf node_modules/* && ../../../../usr/local/n/versions/node/7.2.1/bin/npm install"

# Load the test data:

    sleep 5 # Time for the server to boot
    docker exec -i par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < docker/fresh_drupal_postgres.sql"

# Update Drupal

    docker exec -i par_beta_web bash -c "sh drupal-update.sh /var/www/html"

# Install front end dependencies

    docker exec -i par_beta_web bash -c "rm -rf node_modules/* && ../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm run gulp"
