#!/bin/bash

set -o errexit -euo pipefail -o noclobber -o nounset

cd /var/www/html/
echo "TAG=${CIRCLE_TAG}" >> .env
echo "APP_ENV=testing" >> .env
echo "BUILD_VERSION=testing" >> .env
echo "CHARITY_COMMISSION_API_KEY=${CHARITY_COMMISSION_API_KEY}" >> .env
echo "CLAMAV_HTTP_PASS=${CLAMAV_HTTP_PASS}" >> .env
echo "CLAMAV_HTTP_USER=${CLAMAV_HTTP_USER}" >> .env
echo "COMPANIES_HOUSE_API_KEY=${NP_COMPANIES_HOUSE_API_KEY}" >> .env
echo "IDEAL_POSTCODES_API_KEY=${NP_IDEAL_POSTCODES_API_KEY}" >> .env
echo "PAR_GOVUK_NOTIFY_KEY=${NP_PAR_GOVUK_NOTIFY_KEY}" >> .env
echo "PAR_GOVUK_NOTIFY_TEMPLATE=${NP_PAR_GOVUK_NOTIFY_TEMPLATE}" >> .env
echo "PAR_HASH_SALT=${PAR_HASH_SALT}" >> .env
echo "S3_ACCESS_KEY=${NP_S3_ACCESS_KEY}" >> .env
echo "S3_BUCKET_ARTIFACTS=${S3_BUCKET_ARTIFACTS}" >> .env
echo "S3_BUCKET_PRIVATE=${NP_S3_BUCKET_PRIVATE}" >> .env
echo "S3_BUCKET_PUBLIC=${NP_S3_BUCKET_PUBLIC}" >> .env
echo "S3_REGION=${S3_REGION}" >> .env
echo "S3_SECRET_KEY=${NP_S3_SECRET_KEY}" >> .env
echo "SENTRY_DSN=${SENTRY_DSN}" >> .env
echo "SENTRY_DSN_PUBLIC=${SENTRY_DSN_PUBLIC}" >> .env
echo "SENTRY_ENVIRONMENT=testing" >> .env
echo "SENTRY_RELEASE=testing" >> .env
echo "SENTRY_RELEASE=${CIRCLE_TAG}" >> .env
