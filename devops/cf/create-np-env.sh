#!/bin/bash

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

cf set-env beis-par-test TAG ${CIRCLE_TAG}
cf set-env beis-par-test APP_ENV testing
cf set-env beis-par-test BUILD_VERSION testing
cf set-env beis-par-test CHARITY_COMMISSION_API_KEY ${CHARITY_COMMISSION_API_KEY}
cf set-env beis-par-test CLAMAV_HTTP_PASS ${CLAMAV_HTTP_PASS}
cf set-env beis-par-test CLAMAV_HTTP_USER ${CLAMAV_HTTP_USER}
cf set-env beis-par-test COMPANIES_HOUSE_API_KEY ${NP_COMPANIES_HOUSE_API_KEY}
cf set-env beis-par-test IDEAL_POSTCODES_API_KEY ${NP_IDEAL_POSTCODES_API_KEY}
cf set-env beis-par-test PAR_GOVUK_NOTIFY_KEY ${NP_PAR_GOVUK_NOTIFY_KEY}
cf set-env beis-par-test PAR_GOVUK_NOTIFY_TEMPLATE ${NP_PAR_GOVUK_NOTIFY_TEMPLATE}
cf set-env beis-par-test PAR_HASH_SALT ${PAR_HASH_SALT}
cf set-env beis-par-test S3_ACCESS_KEY ${NP_S3_ACCESS_KEY}
cf set-env beis-par-test S3_BUCKET_ARTIFACTS ${S3_BUCKET_ARTIFACTS}
cf set-env beis-par-test S3_BUCKET_PRIVATE ${NP_S3_BUCKET_PRIVATE}
cf set-env beis-par-test S3_BUCKET_PUBLIC ${NP_S3_BUCKET_PUBLIC}
cf set-env beis-par-test S3_REGION ${S3_REGION}
cf set-env beis-par-test S3_SECRET_KEY ${NP_S3_SECRET_KEY}
cf set-env beis-par-test SENTRY_DSN ${SENTRY_DSN}
cf set-env beis-par-test SENTRY_DSN_PUBLIC ${SENTRY_DSN_PUBLIC}
cf set-env beis-par-test SENTRY_ENVIRONMENT testing
cf set-env beis-par-test SENTRY_RELEASE testing
cf set-env beis-par-test SENTRY_RELEASE ${CIRCLE_TAG}
