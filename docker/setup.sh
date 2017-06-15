# Load the test data:

#    docker exec -i pars_beta_db mysql -upars -p123456 pars < ../sql/20170614_132100.par.db.sanitised.sql
    
# Setup the development settings file:

    docker exec -i pars_beta_web cp /var/www/html/sites/example.settings.local.php /var/www/html/sites/default/settings.local.php
    docker exec -i pars_beta_web cat /var/www/html/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
    
# Set up a soft link to avoid some referencing issues from the Drupal code
 
    docker exec -i pars_beta_web ln -s /var/www/html /var/www/web
    
# Login to the Postgres server (if needed)

#   docker exec -it postgresql sudo -u postgres psql
    
#   docker exec -i pars_beta_db pg_dump -U pars pars > pg_dump.sql






