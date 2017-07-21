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

LOCALPATH=/home/vcap/$FILEPATH
REMOTEPATH=$FILEPATH.tar.gz

echo "Current working directory is ${PROJECT_ROOT}/web"
COMMAND="../vendor/drush/drush/drush sql-dump @$DRUPAL_ENV --result-file=$LOCALPATH"
echo $COMMAND

cd ${PROJECT_ROOT}/web; $COMMAND

COMMAND="tar -zcvf $LOCALPATH.tar.gz $LOCALPATH"
echo $COMMAND

cd ${PROJECT_ROOT}/web; $COMMAND

COMMAND="../vendor/drush/drush/drush fsp s3backups $LOCALPATH.tar.gz $REMOTEPATH"
echo $COMMAND

cd ${PROJECT_ROOT}/web; $COMMAND
