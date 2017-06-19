# Install dependencies

    cd ..
    sh composer.sh install
    cd docker

# Setup the development settings file:

    docker exec -i pars_beta_web cp /var/www/html/web/sites/example.settings.local.php /var/www/html/web/sites/default/settings.local.php
    docker exec -i pars_beta_web cat /var/www/html/web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
    
# Modify the default site's web root
 
    docker exec -it pars_beta_web sed -i -e 's/html/html\/web/g' /etc/apache2/sites-available/000-default.conf
    docker restart pars_beta_web

# Load the test data:
 
    sleep 5 # Time for the server to boot
    docker exec -i pars_beta_web  /var/www/html/vendor/bin/drush sql-cli @dev --root=/var/www/html/web < fresh_drupal_postgres.sql





