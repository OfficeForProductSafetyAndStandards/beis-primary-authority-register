import json, os
import sys
import time

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

outputfilename = '/home/vcap/par-' + str(time.time()) + '.sql'

os.system("../bin/pgsql/bin/pg_dump -h " + host + " -U " + username + " " + name + " > " + outputfilename)
