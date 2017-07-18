TIMESTAMP=$(date -d "today" +"%Y%m%d%H%M")
PRE_UNSANITIZED_FILENAME=drush-dump-pre-drush-updates-unsanitized-$TIMESTAMP.sql
POST_UNSANITIZED_FILENAME=drush-dump-post-drush-updates-unsanitized-$TIMESTAMP.sql
POST_SANITIZED_FILENAME=drush-dump-post-drush-updates-sanitized-$TIMESTAMP.sql
cd /home/vcap/app
sh drupal-dump.sh /home/vcap/app paas-unsanitized $PRE_UNSANITIZED_FILENAME
sh drupal-update.sh /home/vcap/app
sh drupal-dump.sh /home/vcap/app paas-unsanitized $POST_UNSANITIZED_FILENAME
sh drupal-dump.sh /home/vcap/app paas-sanitized $POST_SANITIZED_FILENAME

