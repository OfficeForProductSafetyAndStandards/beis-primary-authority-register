#!/bin/bash
echo $BASH_VERSION
set -o errexit -euo pipefail -o noclobber -o nounset

ROOT="${BASH_SOURCE%/*}/../web"
cd $ROOT
echo "Current working directory is ${PWD}"
echo "Current working directory is ${PWD}"
echo "Clearing the cache..."
drush cr
echo "Putting the site into maintenance mode..."
drush state:set system.maintenance_mode 1
echo "Clearing the cache..."
drush cr
echo "Running db updates..."
drush updb -y
echo "Importing config..."
drush cim -y
drush cr drush
echo "Reverting features..."
drush features:import:all -y
echo "Putting the site out of maintenance mode..."
drush state:set system.maintenance_mode 0
echo "Clearing the cache..."
drush cr
