#!/usr/bin/env bash

# Destroy dependencies

    cd /vagrant/docker
    sudo sh destroy-dependencies.sh

# Install dependencies

    # Update to current node LTS.
    docker exec -it par_beta_web bash -c "n 8"

    # Install yarn.
    docker exec -i par_beta_web bash -c "curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -"
    docker exec -i par_beta_web bash -c "echo 'deb https://dl.yarnpkg.com/debian/ stable main' | tee /etc/apt/sources.list.d/yarn.list"
    docker exec -i par_beta_web bash -c "apt-get install apt-transport-https -y"
    docker exec -i par_beta_web bash -c "apt-get update -y"
    docker exec -i par_beta_web bash -c "apt-get install yarn -y"

    docker exec -i par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'
    docker exec -i par_beta_web bash -c "cd /var/www/html/tests && yarn install --ignore-engines"
    docker exec -i par_beta_web bash -c "yarn install"
    docker exec -i par_beta_web bash -c "npm run gulp"

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
fi

# Load the test data:
    DATAFILE=drush-dump-production-sanitized-latest.sql

    # Time for the server to boot
    sleep 5

    # Must load a database before "cr" and "fsg" commands can be bootstrapped

    docker exec -i par_beta_web bash -c "vendor/bin/drush @dev --root=/var/www/html/web sql-drop -y"
    docker exec -i par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < docker/fresh_drupal_postgres.sql"

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
