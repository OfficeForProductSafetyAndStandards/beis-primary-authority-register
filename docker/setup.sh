# Pull the docker images

    docker-compose up -d --force-recreate

# Install dependencies

    $1 docker exec -ti par_beta_web bash -c 'su - composer -c "cd /var/www/html && php composer.phar install"'

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
    cat ../web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
fi
    
# Install test dependencies
 
    $1 docker exec -it par_beta_web bash -c "cd /var/www/html/tests && rm -rf node_modules && /usr/local/n/versions/node/7.2.1/bin/npm install"

# Load the test data:
 
    sleep 5 # Time for the server to boot
    $1 docker exec -i par_beta_web /var/www/html/vendor/bin/drush sql-cli @dev --root=/var/www/html/web < fresh_drupal_postgres.sql

# Update Drupal

    $1 docker exec -i par_beta_web sh /var/www/html/drupal-update.sh /var/www/html

# Install front end dependencies

    $1 docker exec -it par_beta_web bash -c "rm -rf node_modules && /usr/local/n/versions/node/7.2.1/bin/npm install"
    $1 docker exec -i par_beta_web /usr/local/n/versions/node/7.2.1/bin/npm run gulp





