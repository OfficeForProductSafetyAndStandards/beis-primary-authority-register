## Commands that must be run to update a drupal instance.
## Use as `sh ./drupal-update.sh /var/www/html`

# Pass in the root of the project.
if [ -n "$1" ]; then
  ROOT=$1
else
  echo "Must pass the project root as the first argument";
  exit 1;
fi

echo "Current working directory is ${ROOT}/web"

cd ${ROOT}/web; ../vendor/drush/drush/drush sql-dump --result-file=/home/vcap/drush-dump.sql