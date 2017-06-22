# Install dependencies

if [ ! -f ../vendor/autoload.php ]; then
    cd ..
    sh composer.sh install
    cd docker
    # docker exec -i par_beta_web sh /var/www/html/docker/drupal-update.sh
 fi

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    docker exec -i par_beta_web cp /var/www/html/web/sites/example.settings.local.php /var/www/html/web/sites/default/settings.local.php
    docker exec -i par_beta_web cat /var/www/html/web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
fi
    
# Install test dependencies
 
    docker exec -it par_beta_web bash -c "cd /var/www/html/tests && /usr/local/n/versions/node/7.2.1/bin/npm install"

# Load the test data:
 
    sleep 5 # Time for the server to boot
    docker exec -i par_beta_web /var/www/html/vendor/bin/drush sql-cli @dev --root=/var/www/html/web < fresh_drupal_postgres.sql





