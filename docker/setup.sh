# Load the test data:

    docker exec -i pars_beta_db mysql -upars -p123456 pars < ../sql/20170614_132100.par.db.sanitised.sql
    
# Setup the development settings file:

    docker exec -i pars_beta_web cp /var/www/html/sites/example.settings.local.php /var/www/html/sites/default/settings.local.php
    docker exec -i pars_beta_web cat /var/www/html/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php 
    docker exec -i pars_beta_web ln -s /var/www/html /var/www/web





