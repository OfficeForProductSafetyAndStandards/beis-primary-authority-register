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
  SQL_FILENAME=$3
else
  echo $USAGE;
  exit 1;
fi

TAR_PATH=/home/vcap

rm $SQL_FILENAME.tar.gz
rm $TAR_PATH/$SQL_FILENAME

cd ${PROJECT_ROOT}/web
../vendor/drush/drush/drush sql-dump @$DRUPAL_ENV --result-file=$TAR_PATH/$SQL_FILENAME
ls -la /home/vcap
tar -zcvf $SQL_FILENAME.tar.gz -C $TAR_PATH $SQL_FILENAME
../vendor/drush/drush/drush fsp s3backups $SQL_FILENAME.tar.gz $SQL_FILENAME.tar.gz

rm $SQL_FILENAME.tar.gz
rm $TAR_PATH/$SQL_FILENAME
