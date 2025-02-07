#!/bin/bash

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT

echo "Current working directory is ${PWD}"

drush cr
drush state:set system.maintenance_mode 1
drush cr
drush updb -y
drush cim -y
drush cr drush;
drush features:import:all -y;
drush state:set system.maintenance_mode 0;
drush cr;
