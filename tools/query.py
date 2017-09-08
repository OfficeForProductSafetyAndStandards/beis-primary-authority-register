import json, os
import sys
import argparse

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

f = open("/home/vcap/.pgpass", "w")
f.write(host + ":5432:" + name + ":" + username + ":" + password)
f.close()

os.system("chmod 600 /home/vcap/.pgpass")

os.environ["PHPRC"] = os.environ["HOME"] + "/app/php/etc"
os.environ["PATH"]= os.environ["PATH"] + ":" + os.environ["HOME"] + "/app/php/bin:" + os.environ["HOME"] + "/app/php/sbin"
os.environ["HTTPD_SERVER_ADMIN"] = "admin@localhost"
os.environ["LD_LIBRARY_PATH"] = os.environ["HOME"] + "/app/php/lib"
os.environ["PATH"] = os.environ["PATH"] + ":/home/vcap/app/bin/pgsql/bin"

os.system("/home/vcap/app/bin/pgsql/bin/psql -h " + host + " " + name + " " + username + " -c '" + sys.argv[1] + "'")