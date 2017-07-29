# Commands that must be run to update a drupal instance.
## Use as `sh ./drupal-stub-data.sh /var/www/html`

# Pass in the root of the project.
if [ -n "$1" ]; then
  ROOT=$1
else
  echo "Must pass the project root as the first argument";
  exit 1;
fi

echo "Current working directory is ${ROOT}/web"

# Enable the test content.
cd ${ROOT}/web; ../vendor/drush/drush/drush pm-uninstall par_data_test -y;
cd ${ROOT}/web; ../vendor/drush/drush/drush en par_data_test -y;
# Put the site in maintenance mode.
cd ${ROOT}/web; ../vendor/drush/drush/drush config-set par_data.settings stubbed true -y;
