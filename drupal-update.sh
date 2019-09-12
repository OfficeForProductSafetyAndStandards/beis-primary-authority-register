## Commands that must be run to update a drupal instance.
## Use as `sh ./drupal-update.sh /var/www/html`

# Pass in the root of the project.
if [ -n "$1" ]; then
  ROOT=$1
else
  ROOT=$PWD
fi

echo "Current working directory is ${ROOT}/web"

# Set default drush alias.
# cd ${ROOT}/web; ../vendor/drush/drush/drush site-set @{{ENV}};
# Put the site in maintenance mode.
printf "Enabling maintenance mode...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush sset system.maintenance_mode 1;
# Clear cache
printf "Clearing cache...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush cr;

# Run db updates.
printf "Running database updates...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush updb -y;
# Import configuration twice to fix a problem with config import when new modules are added to 'core.extensions.yml'.
printf "Importing config...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush cim -y; ../vendor/drush/drush/drush cim -y;
# To doubly make sure drush registers features commands.
printf "Clearing drush caches...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush cc drush;
# Revert all features
printf "Reverting features...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush fra -y;

# Take the site out of maintenance mode.
printf "Disabling maintenance mode...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush sset system.maintenance_mode 0;
# Clear cache.
printf "Clearing final cache...\n"
cd ${ROOT}/web; ../vendor/drush/drush/drush cr;
