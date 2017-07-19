USAGE="Usage: drupal-update.sh project_root drupal_env target_filename"

if [ -n "$1" ]; then
  PROJECT_ROOT=$1
else
  echo $USAGE;
  exit 1;
fi

if [ -n "$2" ]; then
  DRUPAL_ENV=$2
else
  echo $USAGE;
  exit 1;
fi

if [ -n "$3" ]; then
  FILEPATH=$3
else
  echo $USAGE;
  exit 1;
fi

echo "Current working directory is ${PROJECT_ROOT}/web"
COMMAND="../vendor/drush/drush/drush sql-dump @$DRUPAL_ENV --result-file=/home/vcap/$FILEPATH"
echo $COMMAND

cd ${PROJECT_ROOT}/web; $COMMAND

