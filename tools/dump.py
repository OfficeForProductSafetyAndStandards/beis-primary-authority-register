## To be called on the PaaS environment to dump the current database

import json, os
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

os.system("../bin/pgsql/bin/pg_dump -h " + host + " -U " + username + " " + name + " > /home/vcap/par.sql")
