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
    
# Modify the default site's web root
 
    docker exec -it par_beta_web sed -i -e 's/html/html\/web/g' /etc/apache2/sites-available/000-default.conf
    docker restart par_beta_web
    
# Install test dependencies
 
    docker exec -it par_beta_web bash -c "cd /var/www/html/tests && /usr/local/n/versions/node/8.1.2/bin/npm install"

# Load the test data:
 
    sleep 5 # Time for the server to boot
    docker exec -i par_beta_web /var/www/html/vendor/bin/drush sql-cli @dev --root=/var/www/html/web < fresh_drupal_postgres.sql





