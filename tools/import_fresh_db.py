#############################################
# Baseline a database in the PaaS environment
#############################################

import json, os
import sys

from subprocess import call

j = json.loads(os.environ.get('VCAP_SERVICES'))

credentials = j["postgres"][0]["credentials"]
host = credentials["host"]
name = credentials["name"]
port = credentials["port"]
username = credentials["username"]
password = credentials["password"]

print "/home/vcap/app/bin/pgsql/bin/psql -h " + host + " " + name + " " + username

os.system("source /home/vcap/app/drupal-update-paas-envs.sh")
os.system("cd /home/vcap/app/web && ../vendor/drush/drush/drush --root=/home/vcap/app/web sql-drop -y")
os.system("/home/vcap/app/bin/pgsql/bin/psql -h " + host + " " + name + " " + username + " < /home/vcap/app/docker/fresh_drupal_postgres.sql")
os.system("cd /home/vcap/app && sh drupal-update.sh /home/vcap/app")

