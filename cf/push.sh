cd ..
cf push -f manifest.$1.yml
source cf/.env.$1
cf set-env par-beta-$1 S3_ACCESS_KEY $S3_ACCESS_KEY
cf set-env par-beta-$1 S3_SECRET_KEY $S3_SECRET_KEY
cf set-env par-beta-$1 PAR_HASH_SALT $PAR_HASH_SALT
cf set-env par-beta-$1 S3_BUCKET_PUBLIC $S3_BUCKET_PUBLIC
cf set-env par-beta-$1 S3_BUCKET_PRIVATE $S3_BUCKET_PRIVATE
cf set-env par-beta-$1 APP_ENV $1
cf set-env par-beta-$1 PAR_GOVUK_NOTIFY_KEY $PAR_GOVUK_NOTIFY_KEY
cf set-env par-beta-$1 PAR_GOVUK_NOTIFY_TEMPLATE $PAR_GOVUK_NOTIFY_TEMPLATE
cf restage par-beta-$1

cf ssh $1 -c "cd app/tools && python extract_postgres_env_vars.py"
cf ssh $1 -c "cd app && source drupal-update-paas-envs.sh && sh drupal-update.sh /home/vcap/app"

if [$1 == "production"]; then
    cf update-service beis-par-cdn-route -c '{"domain": "primary-authority.beis.gov.uk"}'
fi
