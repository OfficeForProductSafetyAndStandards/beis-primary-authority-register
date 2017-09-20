#!/bin/bash

ENV=$1

vault unseal

vault read secret/par/env/$ENV

S3_ACCESS_KEY=`vault read -field=S3_ACCESS_KEY secret/par/env/$ENV`
S3_SECRET_KEY=`vault read -field=S3_SECRET_KEY secret/par/env/$ENV`
PAR_HASH_SALT=`vault read -field=PAR_HASH_SALT secret/par/env/$ENV`
S3_BUCKET_PUBLIC=`vault read -field=S3_BUCKET_PUBLIC secret/par/env/$ENV`
S3_BUCKET_PRIVATE=`vault read -field=S3_BUCKET_PRIVATE secret/par/env/$ENV`
S3_BUCKET_ARTIFACTS=`vault read -field=S3_BUCKET_ARTIFACTS secret/par/env/$ENV`
PAR_GOVUK_NOTIFY_KEY=`vault read -field=PAR_GOVUK_NOTIFY_KEY secret/par/env/$ENV`
PAR_GOVUK_NOTIFY_TEMPLATE=`vault read -field=PAR_GOVUK_NOTIFY_TEMPLATE secret/par/env/$ENV`
CLAMAV_HTTP_USER=`vault read -field=CLAMAV_HTTP_USER secret/par/env/$ENV`
CLAMAV_HTTP_PASS=`vault read -field=CLAMAV_HTTP_PASS secret/par/env/$ENV`
SENTRY_DSN=`vault read -field=SENTRY_DSN secret/par/env/$ENV`
SENTRY_DSN_PUBLIC=`vault read -field=SENTRY_DSN_PUBLIC secret/par/env/$ENV`

vault write secret/par/env/$ENV S3_ACCESS_KEY=$S3_ACCESS_KEY S3_SECRET_KEY=$S3_SECRET_KEY PAR_HASH_SALT=$PAR_HASH_SALT S3_BUCKET_PUBLIC=$S3_BUCKET_PUBLIC S3_BUCKET_PRIVATE=$S3_BUCKET_PRIVATE S3_BUCKET_ARTIFACTS=$S3_BUCKET_ARTIFACTS PAR_GOVUK_NOTIFY_KEY=$PAR_GOVUK_NOTIFY_KEY PAR_GOVUK_NOTIFY_TEMPLATE=$PAR_GOVUK_NOTIFY_TEMPLATE CLAMAV_HTTP_USER=$CLAMAV_HTTP_USER CLAMAV_HTTP_PASS=$CLAMAV_HTTP_PASS SENTRY_DSN=$SENTRY_DSN SENTRY_DSN_PUBLIC=$SENTRY_DSN_PUBLIC

vault read secret/par/env/$ENV

vault seal
