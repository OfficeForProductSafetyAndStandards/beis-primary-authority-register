#!/bin/bash
## Commands that must be run to migrate any outstanding migrations.
## Use as `./drupal-migrate.sh`
echo $BASH_VERSION
set -o errexit -euo pipefail -o noclobber -o nounset

ROOT="${BASH_SOURCE%/*}/../web"
cd $ROOT
echo "Current working directory is ${PWD}"

# Put the site in maintenance mode.
printf "Enabling maintenance mode...\n"
drush state:set system.maintenance_mode 1;
# Clear cache
printf "Clearing cache...\n"
drush cache:rebuild;

# Detect duplicate media entities.
printf "Create file hashes for de-duplication...\n"
drush migrate:duplicate-file-detection par_migrate_advice_files_step1;
drush migrate:duplicate-file-detection par_migrate_inspection_plan_files_step1;

# Run the migrations.
printf "Running migration scripts...\n"
drush migrate:import par_migrate_advice_files_step1;
drush migrate:import par_migrate_inspection_plan_files_step1;
drush migrate:import par_migrate_advice_files_step2;
drush migrate:import par_migrate_inspection_plan_files_step2;

# Take the site out of maintenance mode.
printf "Disabling maintenance mode...\n"
drush state:set system.maintenance_mode 0;
# Clear cache.
printf "Clearing final cache...\n"
drush cache:rebuild;
