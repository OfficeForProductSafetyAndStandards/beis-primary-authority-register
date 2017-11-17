#############################################
# Post deployment updates
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

f = open("/home/vcap/.pgpass", "w")
f.write(host + ":5432:" + name + ":" + username + ":" + password)
f.close()

os.system("chmod 600 /home/vcap/.pgpass")

os.environ["PHPRC"] = os.environ["HOME"] + "/app/php/etc"
os.environ["PATH"]= os.environ["PATH"] + ":" + os.environ["HOME"] + "/app/php/bin:" + os.environ["HOME"] + "/app/php/sbin"
os.environ["HTTPD_SERVER_ADMIN"] = "admin@localhost"
os.environ["LD_LIBRARY_PATH"] = os.environ["HOME"] + "/app/php/lib"
os.environ["PATH"] = os.environ["PATH"] + ":/home/vcap/app/bin/pgsql/bin"

os.system("cd /home/vcap/app && sh drupal-update.sh /home/vcap/app")


