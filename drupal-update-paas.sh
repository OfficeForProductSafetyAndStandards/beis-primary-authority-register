cd /home/vcap/app
sh drupal-update.sh /home/vcap/app
sh drupal-dump.sh /home/vcap/app paas-sanitized drush-dump-post-drush-updates-sanitized-latest.sql

