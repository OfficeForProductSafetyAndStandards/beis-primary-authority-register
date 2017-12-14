#!/usr/bin/env bash

# Destroy dependencies

    cd /vagrant/docker
    if [ -f ../web/sites/default/settings.local.php ]; then
        sudo rm ../web/sites/default/settings.local.php
    fi

# Install dependencies

    docker exec -i par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'
    docker exec -i par_beta_web bash -c "cd /var/www/html/tests && ../../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm run gulp"

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
fi

# Load the test data:
    DATAFILE=drush-dump-production-sanitized-latest.sql

    # Time for the server to boot
    sleep 5

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cc drush"
    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cr"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush fsg s3backups $DATAFILE.tar.gz /dump.sql.tar.gz"
    docker exec -i par_beta_web bash -c "cd / && tar --no-same-owner -zxvf dump.sql.tar.gz"

    docker exec -i par_beta_web bash -c "vendor/bin/drush @dev --root=/var/www/html/web sql-drop -y"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush sql-cli @dev --root=/var/www/html/web < /$DATAFILE && rm /$DATAFILE"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cc drush"
    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cr"

# Update Drupal

    docker exec -i par_beta_web bash -c "sh drupal-update.sh /var/www/html"
