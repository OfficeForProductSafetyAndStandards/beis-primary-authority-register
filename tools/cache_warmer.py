#############################################
# Cache warming wrapper
#############################################

import json, os
import sys

from subprocess import call

os.environ["PHPRC"] = os.environ["HOME"] + "/app/php/etc"
os.environ["PATH"]= os.environ["PATH"] + ":" + os.environ["HOME"] + "/app/php/bin:" + os.environ["HOME"] + "/app/php/sbin"
os.environ["HTTPD_SERVER_ADMIN"] = "admin@localhost"
os.environ["LD_LIBRARY_PATH"] = os.environ["HOME"] + "/app/php/lib"
os.environ["PATH"] = os.environ["PATH"] + ":/home/vcap/app/bin/pgsql/bin"

os.system("cd /home/vcap/app/web && ../vendor/drush/drush/drush pcw")


