#!/bin/sh

################################################################
# This script will run the Drupal scheduler every 60 seconds
# It should be initiated as a task using
#
# cf run-task par-beta-$ENV "/home/vcap/app/tools/scheduler.sh"
################################################################

while true
do
    if [ $APP_ENV == "production" ]; then
        URL="https://primary-authority.beis.gov.uk"
    else
        URL="https://par-beta-$APP_ENV.cloudapps.digital"
    fi

    URL+="/admin/config/system/cron"
    wget $URL
    sleep 60
done
