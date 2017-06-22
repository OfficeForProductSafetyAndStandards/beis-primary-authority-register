## Commands that must be run to update a drupal instance.
## Use as `sh ./docker/drupal-update.sh /var/www/par-beta`

# Pass in the root of the project.
if [ -n "$1" ]
then
  ROOT=$1
else
  echo "Must pass the project root as the first argument";
  exit 1;
fi

echo "Current working directory is ${ROOT}/web"

# Set default drush alias.
# cd ${ROOT}/web; ../vendor/drush/drush/drush site-set @{{ENV}};
# Put the site in maintenance mode.
cd ${ROOT}/web; ../vendor/drush/drush/drush sset system.maintenance_mode 1;
# Clear cache
cd ${ROOT}/web; ../vendor/drush/drush/drush cr;
# Run db updates.
cd ${ROOT}/web; ../vendor/drush/drush/drush updb -y;
# Import configuration twice to fix a problem with config import when new modules are added to 'core.extensions.yml'.
cd ${ROOT}/web; ../vendor/drush/drush/drush cim -y; ../vendor/drush/drush/drush cim -y
# Take the site out of maintenance mode.
cd ${ROOT}/web; ../vendor/drush/drush/drush sset system.maintenance_mode 0;
# Clear cache.
cd ${ROOT}/web; ../vendor/drush/drush/drush cr;