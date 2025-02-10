#!/bin/bash

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

cf set-env beis-par-staging TAG ${CIRCLE_TAG}
cf set-env beis-par-staging APP_ENV staging
cf set-env beis-par-staging BUILD_VERSION staging
cf set-env beis-par-staging CHARITY_COMMISSION_API_KEY ${CHARITY_COMMISSION_API_KEY}
cf set-env beis-par-staging CLAMAV_HTTP_PASS ${CLAMAV_HTTP_PASS}
cf set-env beis-par-staging CLAMAV_HTTP_USER ${CLAMAV_HTTP_USER}
cf set-env beis-par-staging COMPANIES_HOUSE_API_KEY ${STAGING_COMPANIES_HOUSE_API_KEY}
cf set-env beis-par-staging IDEAL_POSTCODES_API_KEY ${STAGING_IDEAL_POSTCODES_API_KEY}
cf set-env beis-par-staging PAR_GOVUK_NOTIFY_KEY ${STAGING_PAR_GOVUK_NOTIFY_KEY}
cf set-env beis-par-staging PAR_GOVUK_NOTIFY_TEMPLATE ${STAGING_PAR_GOVUK_NOTIFY_TEMPLATE}
cf set-env beis-par-staging PAR_HASH_SALT ${PAR_HASH_SALT}
cf set-env beis-par-staging S3_ACCESS_KEY ${STAGING_S3_ACCESS_KEY}
cf set-env beis-par-staging S3_BUCKET_ARTIFACTS ${S3_BUCKET_ARTIFACTS}
cf set-env beis-par-staging S3_BUCKET_PRIVATE ${STAGING_S3_BUCKET_PRIVATE}
cf set-env beis-par-staging S3_BUCKET_PUBLIC ${STAGING_S3_BUCKET_PUBLIC}
cf set-env beis-par-staging S3_REGION ${S3_REGION}
cf set-env beis-par-staging S3_SECRET_KEY ${STAGING_S3_SECRET_KEY}
cf set-env beis-par-staging SENTRY_DSN ${SENTRY_DSN}
cf set-env beis-par-staging SENTRY_DSN_PUBLIC ${SENTRY_DSN_PUBLIC}
cf set-env beis-par-staging SENTRY_ENVIRONMENT staging
cf set-env beis-par-staging SENTRY_RELEASE staging
cf set-env beis-par-staging SENTRY_RELEASE ${CIRCLE_TAG}
cf re-stage beis-par-staging
