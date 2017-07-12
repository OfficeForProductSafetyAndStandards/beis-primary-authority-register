## To be called on the PaaS environment to dump the current database

import json, os
import boto
import boto.s3
import sys
from boto.s3.key import Key
import datetime

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

outputfilename = '/home/vcap/par.sql'

os.system("../bin/pgsql/bin/pg_dump -h " + host + " -U " + username + " " + name + " > " + outputfilename)

AWS_ACCESS_KEY_ID = os.environ.get('S3_ACCESS_KEY')
AWS_SECRET_ACCESS_KEY = os.environ.get('S3_SECRET_KEY')
S3_BUCKET = os.environ.get('S3_BUCKET')

conn = boto.connect_s3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY)

bucket = conn.create_bucket(S3_BUCKET, location=boto.s3.connection.Location.DEFAULT)

print 'Uploading %s to Amazon S3 bucket %s' % \
   (outputfilename, S3_BUCKET)

def percent_cb(complete, total):
    sys.stdout.write('.')
    sys.stdout.flush()

k = Key(bucket, "par-" + datetime.datetime.utcnow() + ".sql")
k.set_contents_from_filename(testfile, cb=percent_cb, num_cb=10)
