#!/bin/sh

################################################################
# This script will run the Drupal scheduler every 60 seconds
# It should be initiated as a task using
#
# cf run-task par-beta-$ENV "/home/vcap/app/tools/scheduler.sh"
################################################################

while true
do
    cd /home/vcap/app/web
    ../vendor/drush/drush/drush
    sleep 60
done
