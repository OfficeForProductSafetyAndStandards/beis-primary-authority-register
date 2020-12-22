## Commands that must be run to update a drupal instance.
## Use as `./drupal-update.sh`

ROOT="${BASH_SOURCE%/*}/web"
cd $ROOT
echo "Current working directory is ${PWD}"

# Set default drush alias.
# cd ${ROOT}/web; ../vendor/drush/drush/drush site-set @{{ENV}};
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
# Import configuration twice to fix a problem with config import when new modules are added to 'core.extensions.yml'.
printf "Importing config...\n"
../vendor/drush/drush/drush config:import -y; ../vendor/drush/drush/drush cim -y;
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
