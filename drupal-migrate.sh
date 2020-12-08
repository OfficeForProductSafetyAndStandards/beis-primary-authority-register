## Commands that must be run to update a drupal instance.
## Use as `sh ./drupal-migrate.sh`

ROOT="${BASH_SOURCE%/*}/web"
cd $ROOT
echo "Current working directory is ${PWD}"

# Put the site in maintenance mode.
printf "Enabling maintenance mode...\n"
../vendor/drush/drush/drush state:set system.maintenance_mode 1;
# Clear cache
printf "Clearing cache...\n"
../vendor/drush/drush/drush cache:rebuild;

# Detect duplicate media entities.
printf "Create file hashes for de-duplication...\n"
../vendor/drush/drush/drush migrate:duplicate-file-detection par_migrate_advice_files_step1;
../vendor/drush/drush/drush migrate:duplicate-file-detection par_migrate_inspection_plan_files_step1;

# Run the migrations.
printf "Running migration scripts...\n"
../vendor/drush/drush/drush migrate:import par_migrate_advice_files_step1;
../vendor/drush/drush/drush migrate:import par_migrate_inspection_plan_files_step1;
../vendor/drush/drush/drush migrate:import par_migrate_advice_files_step2;
../vendor/drush/drush/drush migrate:import par_migrate_inspection_plan_files_step2;

# Take the site out of maintenance mode.
printf "Disabling maintenance mode...\n"
../vendor/drush/drush/drush state:set system.maintenance_mode 0;
# Clear cache.
printf "Clearing final cache...\n"
../vendor/drush/drush/drush cache:rebuild;
