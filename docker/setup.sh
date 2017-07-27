#!/usr/bin/env bash

# Destroy dependencies

    cd /vagrant/docker
    sudo sh destroy-dependencies.sh

# Install dependencies

    docker exec -i par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'
    docker exec -i par_beta_web bash -c "cd /var/www/html/tests && rm -rf node_modules/* && ../../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "rm -rf node_modules/* && ../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm run gulp"

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
    cat ../web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
fi

# Load the test data:

    DATAFILE=drush-dump-post-drush-updates-sanitized-201707270857.sql
    
    # Time for the server to boot
    sleep 5

    docker exec -i par_beta_web bash -c "vendor/bin/drush @dev --root=/var/www/html/web sql-drop -y"
    docker exec -i par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < docker/fresh_drupal_postgres.sql"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cc drush"
    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cr"

# Update Drupal

    docker exec -i par_beta_web bash -c "sh drupal-update.sh /var/www/html"
