#!/bin/bash
## Commands that must be run to update a drupal instance.
## Use as `./drupal-update.sh`
echo $BASH_VERSION
set -o errexit -euo pipefail -o noclobber -o nounset

ROOT="${BASH_SOURCE%/*}/web"
cd $ROOT;
echo "Current working directory is ${PWD}"

# Put the site in maintenance mode.
printf "Enabling maintenance mode...\n"
../vendor/drush/drush/drush state:set system.maintenance_mode 1;
# Clear cache
printf "Clearing cache...\n"
../vendor/drush/drush/drush cache:rebuild;

# Test data must be removed before proceeding.
printf "Uninstalling test data...\n"
../vendor/drush/drush/drush pm-uninstall par_data_test -y;

# Run db updates.
printf "Running database updates...\n"
../vendor/drush/drush/drush updb -y;

## CONFIG IMPORT
printf "Importing config...\n"
counter=1;
# Check that there's no remaining config diff.
until ../vendor/drush/drush/drush --quiet --no config:import &> /dev/null
do
  if [ $counter -gt 5 ]; then
      echo "Config successfully imported after #$counter tries"
      break
  fi
  echo "Trying import: #$counter"
  # This import is allowed to fail.
  ../vendor/drush/drush/drush config:import -y || true
  ((counter++))
  sleep 1;
done
# Run config import a second time to avoid installed
# module config overriding saved config.
printf "Re-importing config...\n"
../vendor/drush/drush/drush config:import -y

# To doubly make sure drush registers features commands.
printf "Clearing drush caches...\n"
../vendor/drush/drush/drush cache:clear drush;
# Revert all features
printf "Reverting features...\n"
../vendor/drush/drush/drush features:import:all -y;

# Take the site out of maintenance mode.
printf "Disabling maintenance mode...\n"
../vendor/drush/drush/drush state:set system.maintenance_mode 0;

# Clear cache.
printf "Clearing final cache...\n"
../vendor/drush/drush/drush cache:rebuild;
