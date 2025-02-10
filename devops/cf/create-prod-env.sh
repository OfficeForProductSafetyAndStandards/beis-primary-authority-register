#!/bin/bash

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

cf set-env beis-par-production TAG ${CIRCLE_TAG}
cf set-env beis-par-production APP_ENV=production
cf set-env beis-par-production BUILD_VERSION=production
cf set-env beis-par-production CHARITY_COMMISSION_API_KEY ${CHARITY_COMMISSION_API_KEY}
cf set-env beis-par-production CLAMAV_HTTP_PASS ${CLAMAV_HTTP_PASS}
cf set-env beis-par-production CLAMAV_HTTP_USER ${CLAMAV_HTTP_USER}
cf set-env beis-par-production COMPANIES_HOUSE_API_KEY ${PROD_COMPANIES_HOUSE_API_KEY}
cf set-env beis-par-production IDEAL_POSTCODES_API_KEY ${PROD_IDEAL_POSTCODES_API_KEY}
cf set-env beis-par-production PAR_GOVUK_NOTIFY_KEY ${PROD_PAR_GOVUK_NOTIFY_KEY}
cf set-env beis-par-production PAR_GOVUK_NOTIFY_TEMPLATE ${PROD_PAR_GOVUK_NOTIFY_TEMPLATE}
cf set-env beis-par-production PAR_HASH_SALT ${PAR_HASH_SALT}
cf set-env beis-par-production S3_ACCESS_KEY ${PROD_S3_ACCESS_KEY}
cf set-env beis-par-production S3_BUCKET_ARTIFACTS ${S3_BUCKET_ARTIFACTS}
cf set-env beis-par-production S3_BUCKET_PRIVATE ${PROD_S3_BUCKET_PRIVATE}
cf set-env beis-par-production S3_BUCKET_PUBLIC ${PROD_S3_BUCKET_PUBLIC}
cf set-env beis-par-production S3_REGION ${S3_REGION}
cf set-env beis-par-production S3_SECRET_KEY ${PROD_S3_SECRET_KEY}
cf set-env beis-par-production SENTRY_DSN ${SENTRY_DSN}
cf set-env beis-par-production SENTRY_DSN_PUBLIC ${SENTRY_DSN_PUBLIC}
cf set-env beis-par-production SENTRY_ENVIRONMENT=production
cf set-env beis-par-production SENTRY_RELEASE=production
cf set-env beis-par-production SENTRY_RELEASE ${CIRCLE_TAG}
