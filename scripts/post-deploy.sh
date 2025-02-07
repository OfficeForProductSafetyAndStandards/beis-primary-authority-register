#!/bin/bash
## For dropping a database.
## Use as `./post-deploy.sh`
echo $BASH_VERSION
set -o errexit -euo pipefail -o noclobber -o nounset

ROOT="${BASH_SOURCE%/*}/../web"
cd $ROOT
echo "Current working directory is ${PWD}"

# Put the site in maintenance mode.
printf "Enabling maintenance mode...\n"
drush cr;
drush state:set system.maintenance_mode 1 --input-format=integer;
# Clear cache
printf "Clearing cache...\n"
drush cr;

# Run db updates.
printf "Running database updates...\n"
drush updb -y;

## CONFIG IMPORT
printf "Importing config...\n"
counter=1;
# Check that there's no remaining config diff.
until drush --quiet --no cim &> /dev/null
do
  if [ $counter -gt 5 ]; then
      echo "Config successfully imported after #$counter tries"
      break
  fi
  echo "Trying import: #$counter"
  # This import is allowed to fail.
  drush cim -y || true
  ((counter++))
  sleep 1;
done
# Run config import a second time to avoid installed
# module config overriding saved config.
printf "Re-importing config...\n"
drush cim -y

# To doubly make sure drush registers features commands.
printf "Clearing drush caches...\n"
drush cr drush;
# Revert all features
printf "Reverting features...\n"
drush features:import:all -y;

# Take the site out of maintenance mode.
printf "Disabling maintenance mode...\n"
drush state:set system.maintenance_mode 0;

# Clear cache.
printf "Clearing final cache...\n"
drush cr;


