#!bin/bash

PATH=$PATH:/home/vcap/app/bin/pgsql/bin
HOME=$HOME/app sh /home/vcap/app/.profile.d/bp_env_vars.sh
cd /home/vcap/app
sh drupal-update.sh /home/vcap/app

