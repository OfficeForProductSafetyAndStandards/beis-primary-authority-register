#!/usr/bin/env bash

# Install dependencies

    docker exec -i par_beta_web bash -c 'su - composer -c "php composer.phar install"'
    docker exec -i par_beta_web bash -c "cd /var/www/html/tests && npm install"
    docker exec -i par_beta_web bash -c "npm install"
    docker exec -i par_beta_web bash -c "npm run gulp"

# Load the test data:
    DATAFILE=drush-dump-production-sanitized-latest.sql

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush fsg s3backups $DATAFILE.tar.gz /dump.sql.tar.gz && cd / && tar --no-same-owner -zxvf dump.sql.tar.gz"

    docker exec -i par_beta_web bash -c "vendor/bin/drush @dev --root=/var/www/html/web sql-drop -y"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush sql-cli @dev --root=/var/www/html/web < /$DATAFILE && rm /$DATAFILE"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cc drush"

# Update Drupal

    docker exec -i par_beta_web bash -c "sh drupal-update.sh /var/www/html"
