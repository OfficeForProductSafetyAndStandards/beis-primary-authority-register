#######################################################################
# This script will run the Drupal scheduler every 60 seconds
# It should be initiated as a task using
#
# cf run-task beis-par-$ENV "python /home/vcap/app/devops/tools/scheduler.py"
#######################################################################

import os, sys
import sched, time

from subprocess import call

os.environ["PHPRC"] = os.environ["HOME"] + "/app/php/etc"
os.environ["PATH"]= os.environ["PATH"] + ":" + os.environ["HOME"] + "/app/php/bin:" + os.environ["HOME"] + "/app/php/sbin"
os.environ["HTTPD_SERVER_ADMIN"] = "admin@localhost"
os.environ["LD_LIBRARY_PATH"] = os.environ["HOME"] + "/app/php/lib"
os.environ["PATH"] = os.environ["PATH"] + ":/home/vcap/app/bin/pgsql/bin"

s = sched.scheduler(time.time, time.sleep)

def run_cron(sc):
    os.system("cd /home/vcap/app/web; ../vendor/drush/drush/drush cron;")
    s.enter(60, 1, run_cron, (sc,))
    
s.enter(60, 1, run_cron, (s,))
s.run()    
