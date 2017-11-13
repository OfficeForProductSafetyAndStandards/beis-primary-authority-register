#############################################
# Post deployment updates
#############################################

import os
import sys

from subprocess import call

os.system("cd /home/vcap/app/web && ../vendor/drush/drush/drush pcw")


