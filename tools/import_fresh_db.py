#############################################
# Baseline a database in the PaaS environment
#############################################

import json, os
import sys

from subprocess import call

if os.environ["APP_ENV"] == "production":
  print "Please don't run this on production!"
  sys.exit()

j = json.loads(os.environ.get('VCAP_SERVICES'))

credentials = j["postgres"][0]["credentials"]
host = credentials["host"]
name = credentials["name"]
port = credentials["port"]
username = credentials["username"]
password = credentials["password"]

os.environ["PHPRC"] = os.environ["HOME"] + "/app/php/etc"
os.environ["PATH"]= os.environ["PATH"] + ":" + os.environ["HOME"] + "/app/php/bin:" + os.environ["HOME"] + "/app/php/sbin"
os.environ["HTTPD_SERVER_ADMIN"] = "admin@localhost"
os.environ["LD_LIBRARY_PATH"] = os.environ["HOME"] + "/app/php/lib"
os.environ["PATH"] = os.environ["PATH"] + ":/home/vcap/app/bin/pgsql/bin"

os.system("cd /home/vcap/app/web && ../vendor/drush/drush/drush --root=/home/vcap/app/web sql-drop -y")
os.system("/home/vcap/app/bin/pgsql/bin/psql -h " + host + " " + name + " " + username + " < /home/vcap/app/docker/fresh_drupal_postgres.sql")
os.system("cd /home/vcap/app && sh drupal-update.sh /home/vcap/app")

