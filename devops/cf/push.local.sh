#!/bin/bash
# This script will push local assets to an environment.
# Usage: ./push.local.sh -r -b /path/to/database/backup/${SANITISED_DATABASE} -d /path/to/build/directory $DEPLOY_ENV

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset


####################################################################################
# Prerequisites - You'll need the following installed
#    Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html
#    Vault CLI - https://www.vaultproject.io/docs/install/index.html
####################################################################################
! getopt --test > /dev/null
if [[ ${PIPESTATUS[0]} -ne 4 ]]; then
    echo "################################################################################################"
    echo >&2 'Error: `getopt --test` failed in this environment.'
    echo "################################################################################################"

    exit 1
fi

command -v cf >/dev/null 2>&1 || {
    echo "################################################################################################"
    echo >&2 "Please install Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html"
    echo "################################################################################################"
    exit 1
}

####################################################################################
# Set required parameters
#    ENV (required) - the password for the user account
#    BUILD_VER (optional) - the build tag being pushed
#    DEV_GOVUK_CF_USER (required) - the user deploying the script
#    DEV_GOVUK_CF_PWD (required) - the password for the user account
#    BUILD_DIR - the directory containing the build assets
####################################################################################
OPTIONS=sT:u:p:i:b:rd:v:n:t:
LONGOPTS=single,build-tag:,user:,password:,instances:,database:,refresh-database,directory:,token:,deploy-production,

# -use ! and PIPESTATUS to get exit code with errexit set
# -temporarily store output to be able to check for errors
# -activate quoting/enhanced mode (e.g. by writing out “--options”)
# -pass arguments only via   -- "$@"   to separate them correctly
! PARSED=$(getopt --options=$OPTIONS --longoptions=$LONGOPTS --name "$0" -- "$@")
if [[ ${PIPESTATUS[0]} -ne 0 ]]; then
    # e.g. return value is 1
    #  then getopt has complained about wrong arguments to stdout
    exit 2
fi
# read getopt’s output this way to handle the quoting right:
eval set -- "$PARSED"

# Defaults
ENV_ONLY=${ENV_ONLY:=n}
DEV_GOVUK_CF_USER=${DEV_GOVUK_CF_USER:-}
DEV_GOVUK_CF_PWD=${DEV_GOVUK_CF_PWD:-}
CF_INSTANCES=${CF_INSTANCES:=1}
BUILD_VER=${BUILD_VER:-}
BUILD_DIR=${BUILD_DIR:=$PWD}
REMOTE_BUILD_DIR=${REMOTE_BUILD_DIR:="/home/vcap/app"}
DB_NAME="db-seed"
DB_DIR="backups"
DB_RESET=${DB_RESET:=n}
DEPLOY_PRODUCTION=${DEPLOY_PRODUCTION:=n}
CHARITY_COMMISSION_API_KEY=${CHARITY_COMMISSION_API_KEY:-}
CLAMAV_HTTP_PASS=${CLAMAV_HTTP_PASS:-}
CLAMAV_HTTP_USER=${CLAMAV_HTTP_USER:-}
TAG=${CIRCLE_TAG:-}
PAR_HASH_SALT=${PAR_HASH_SALT:-}
S3_REGION=${S3_REGION:-}
SENTRY_DSN=${SENTRY_DSN:-}
SENTRY_DSN_PUBLIC=${SENTRY_DSN_PUBLIC:-}
SENTRY_RELEASE=${CIRCLE_TAG:-}
STAGING_S3_SECRET_KEY=${STAGING_S3_SECRET_KEY:-}
STAGING_S3_ACCESS_KEY=${STAGING_S3_ACCESS_KEY:-}
STAGING_S3_BUCKET_ARTIFACTS=${S3_BUCKET_ARTIFACTS:-}
STAGING_S3_BUCKET_PRIVATE=${STAGING_S3_BUCKET_PRIVATE:-}
STAGING_S3_BUCKET_PUBLIC=${STAGING_S3_BUCKET_PUBLIC:-}
STAGING_COMPANIES_HOUSE_API_KEY=${STAGING_COMPANIES_HOUSE_API_KEY}:-}
STAGING_IDEAL_POSTCODES_API_KEY=${STAGING_IDEAL_POSTCODES_API_KEY:-}
STAGING_PAR_GOVUK_NOTIFY_KEY=${STAGING_PAR_GOVUK_NOTIFY_KEY:-}
STAGING_PAR_GOVUK_NOTIFY_TEMPLATE=${STAGING_PAR_GOVUK_NOTIFY_TEMPLATE:-}

while true; do
    case "$1" in
        -s|--single)
            ENV_ONLY=y
            shift
            ;;
        -T|--build-tag)
            BUILD_VER="$2"
            shift 2
            ;;
        -u|--user)
            DEV_GOVUK_CF_USER="$2"
            shift 2
            ;;
        -p|--password)
            DEV_GOVUK_CF_PWD="$2"
            shift 2
            ;;
        -i|--instances)
            CF_INSTANCES="$2"
            shift 2
            ;;
        -b|--database)
            DB_IMPORT="$2"
            shift 2
            ;;
        -r|--refresh-database)
            DB_RESET=y
            shift
            ;;
        -x|--deploy-production)
            DEPLOY_PRODUCTION=y
            shift
            ;;
        -d|--directory)
            BUILD_DIR="$2"
            shift 2
            ;;
        -v|--vault)
            VAULT_ADDR="$2"
            shift 2
            ;;
        -n|--unseal)
            VAULT_UNSEAL="$2"
            shift 2
            ;;
        -t|--token)
            VAULT_TOKEN="$2"
            shift 2
            ;;
        --)
            shift
            break
            ;;
        *)
            echo "Programming error"
            exit 3
            ;;
    esac
done

# Defaults that incorporate user defined values
DB_IMPORT=${DB_IMPORT:="$BUILD_DIR/$DB_DIR/$DB_NAME.sql"}

## Ensure an environment has been passed
if [[ $# -ne 1 ]]; then
    echo "Please specify the environment to push to."
    exit 4
fi
ENV=$1

## Automated deployment to production needs to be to the production environment.
if [[ $ENV == 'production' ]] && [[ $DEPLOY_PRODUCTION != 'y' ]]; then
    read -r -p "Are you sure you wish to deploy to production? [y/N] " response
    case "$response" in
        [yY][eE][sS]|[yY])
            echo "Deploying to production."
            ;;
        *)
            echo "Deployment to production isn't supported at this time."
            exit 11
            ;;
    esac
fi


####################################################################################
# Login to GovUK PaaS
####################################################################################
printf "Authenticating with GovUK PaaS...\n"

if [[ $ENV == 'production' ]] || [[ $ENV =~ ^production-.* ]]; then
    cf login -a api.cloud.service.gov.uk -u $DEV_GOVUK_CF_USER -p $DEV_GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-production"
elif [[ $ENV == 'staging' ]] || [[ $ENV =~ ^staging-.* ]]; then
    cf login -a api.cloud.service.gov.uk -u $DEV_GOVUK_CF_USER -p $DEV_GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-staging"
else
    cf login -a api.cloud.service.gov.uk -u $DEV_GOVUK_CF_USER -p $DEV_GOVUK_CF_PWD \
      -o office-for-product-safety-and-standards -s primary-authority-register-development
fi


####################################################################################
# Configure the application
# Some values van be set based on the variables provided to this script
# others need to be provided in the form of an app manifest
# To understand manifest configuration see https://docs.cloudfoundry.org/devguide/deploy-apps/manifest.html
####################################################################################
printf "Configuring the application...\n"

if [[ $ENV_ONLY == y ]]; then
    TARGET_ENV=beis-par-$ENV
else
    TARGET_ENV=beis-par-$ENV-green
    BLUE_ENV=beis-par-$ENV
fi

PG_BACKING_SERVICE="par-pg-$ENV"
CDN_BACKING_SERVICE="par-cdn-$ENV"
REDIS_BACKING_SERVICE="par-redis-$ENV"
OS_BACKING_SERVICE="par-os-$ENV"
LOGGING_BACKING_SERVICE="opss-log-drain"

MANIFEST="${BASH_SOURCE%/*}/manifests/manifest.$ENV.yml"
if [[ ! -f $MANIFEST ]]; then
    MANIFEST="${BASH_SOURCE%/*}/manifests/manifest.non-production.yml"
fi

## Copy the seed database to the build directory and archive it for import.
printf "Archiving the seed database in $BUILD_DIR/$DB_DIR...\n"
mkdir -p "$BUILD_DIR/$DB_DIR"
if [[ -f $DB_IMPORT ]]; then
    cp "$DB_IMPORT" "$BUILD_DIR/$DB_DIR/$DB_NAME.sql"
    tar -zcvf "$BUILD_DIR/$DB_DIR/$DB_NAME.tar.gz" -C $BUILD_DIR/$DB_DIR "$DB_NAME.sql"
fi

####################################################################################
# Cleanup any instances that have been created
#
# If this script exists with an error remove any GovUK PaaS instances created.
#
# In non-production environments it doesn't really matter if we remove the entire
# environment because we can just create it again, there are no known persistent
# non-production environments. If there are any created these should be added
# to the exception list.
####################################################################################
function cf_teardown {
    if [[ $ENV != "production" ]] && [[ $ENV != "staging" ]]; then

        ## Remove any postgres backing services, unbind services first
        if cf service $PG_BACKING_SERVICE >/dev/null 2>&1; then
            if cf app beis-par-$ENV >/dev/null 2>&1; then
                cf unbind-service beis-par-$ENV $PG_BACKING_SERVICE
            fi
            if [[ $ENV_ONLY != y ]] && cf app beis-par-$ENV-green >/dev/null 2>&1; then
                cf unbind-service beis-par-$ENV-green $PG_BACKING_SERVICE
            fi

            ## In some instances service keys may also have to be deleted
            printf "If there are any service keys these will need to be deleted manually, see 'cf service-keys $PG_BACKING_SERVICE'\n"

            cf delete-service -f $PG_BACKING_SERVICE
        fi

        ## Remove any redis backing services, unbind services first
        if cf service $REDIS_BACKING_SERVICE >/dev/null 2>&1; then
            if cf app beis-par-$ENV >/dev/null 2>&1; then
                cf unbind-service beis-par-$ENV $REDIS_BACKING_SERVICE
            fi
            if [[ $ENV_ONLY != y ]] && cf app beis-par-$ENV-green >/dev/null 2>&1; then
                cf unbind-service beis-par-$ENV-green $REDIS_BACKING_SERVICE
            fi

            ## In some instances service keys may also have to be deleted
            printf "If there are any service keys these will need to be deleted manually, see 'cf service-keys $REDIS_BACKING_SERVICE'\n"

            cf delete-service -f $REDIS_BACKING_SERVICE
        fi

        ## Remove any opensearch backing services, unbind services first
        if cf service $OS_BACKING_SERVICE >/dev/null 2>&1; then
            if cf app beis-par-$ENV >/dev/null 2>&1; then
                cf unbind-service beis-par-$ENV $OS_BACKING_SERVICE
            fi
            if [[ $ENV_ONLY != y ]] && cf app beis-par-$ENV-green >/dev/null 2>&1; then
                cf unbind-service beis-par-$ENV-green $OS_BACKING_SERVICE
            fi

            ## In some instances service keys may also have to be deleted
            printf "If there are any service keys these will need to be deleted manually, see 'cf service-keys $OS_BACKING_SERVICE'\n"

            cf delete-service -f $OS_BACKING_SERVICE
        fi

        ## Remove the main app if it exists
        if cf app beis-par-$ENV >/dev/null 2>&1; then
            cf delete -f beis-par-$ENV
        fi

        ## Remove any instantiated green instances
        if [[ $ENV_ONLY != y ]] && cf app beis-par-$ENV-green >/dev/null 2>&1; then
            cf delete -f beis-par-$ENV-green
        fi

        printf "################################################################################################\n"
        printf >&2 "This script failed to build and is tearing down any non-production instances.\n"
        printf >&2 "This could take up to 10 minutes, please do not try to rebuild until this is complete.\n"
        printf >&2 "You can check the progress by running 'cf service $PG_BACKING_SERVICE' and 'cf app beis-par-$ENV'.\n"
        printf "################################################################################################\n"

    fi
}
trap cf_teardown ERR


####################################################################################
# Create polling function
# Used to check for the status of a PaaS service or a PaaS task.
####################################################################################
function cf_poll_app {
    I=1
    printf "Waiting for the app $1...\n"
    while [[ $(cf app $1 | awk -F '  +' '/status:/ {print $2}' | grep 'in progress') ]]
    do
      printf "%0.s-" $(seq 1 $I)
      sleep 2
    done
    printf "App $1 is running...\n"
}
function cf_poll_service {
    I=1
    printf "Waiting for $1 backing service...\n"
    while [[ $(cf service $1 | awk -F '  +' '/status:/ {print $3}' | grep 'in progress') ]]
    do
      printf "%0.s-" $(seq 1 $I)
      sleep 2
    done
    printf "Backing service $1 is running...\n"
}
function cf_poll_task {
    I=1
    printf "Waiting for $2 task...\n"
    while [[ $(cf tasks $1 | awk '//{print $2, $3}' | grep "$2 RUNNING") ]]
    do
      printf "%0.s-" $(seq 1 $I)
      sleep 2
    done
    task_status=$(cf tasks $1 | awk '//{print $1, $2, $3}' | grep -m 1 "$2" | awk '//{print $3}')
    if [[ $task_status == "FAILED" ]]; then
      printf "Task $2 has failed...\n"
      cf logs $1 --recent | tail -500
      exit 99
    fi
    printf "Task $2 has completed ($task_status)...\n"
}


####################################################################################
# Waiting for cloud foundry to be ready
# If an existing process is already in progress for this environment then wait
# for it's completion before continuing.
####################################################################################
printf "Waiting for cloud foundry (readiness)...\n"

## Checking the app
cf_poll_app $TARGET_ENV

## Checking the postgres backing services
cf_poll_service $PG_BACKING_SERVICE
## Checking the redis backing services
cf_poll_service $REDIS_BACKING_SERVICE
## Checking the opensearch backing services
cf_poll_service $OS_BACKING_SERVICE


####################################################################################
# Start the app
# And set the environment variables. Even though php will read from the .env file
# setting the cf variables directly allows them to be accessed by other scripts
# see https://docs.cloud.service.gov.uk/deploying_apps.html#deploying-an-app
####################################################################################
printf "Pushing the application...\n"

export COMPOSER_VENDOR_DIR={BUILD_DIR}/vendor
cf push --no-start -f $MANIFEST -p $BUILD_DIR --var app=$TARGET_ENV $TARGET_ENV

## Set the cf environment variables directly
printf "Setting the environment variables...\n"

# Set the additional app_env variables.
cf set-env $TARGET_ENV APP_ENV $ENV
cf set-env $TARGET_ENV SENTRY_ENVIRONMENT $ENV

# Ensure that the sentry release is also set.
if [[ ! -z "${BUILD_VER}" ]]; then
  cf set-env $TARGET_ENV BUILD_VERSION $ENV
  cf set-env $TARGET_ENV SENTRY_RELEASE ${CIRCLE_TAG}
  cf set-env $TARGET_ENV SENTRY_DSN ${SENTRY_DSN}
  cf set-env $TARGET_ENV SENTRY_DSN_PUBLIC ${SENTRY_DSN_PUBLIC}
fi

# Set all other env vars applicable to all environments
cf set-env $TARGET_ENV PAR_HASH_SALT ${PAR_HASH_SALT}
cf set-env $TARGET_ENV TAG ${CIRCLE_TAG}
cf set-env $TARGET_ENV CHARITY_COMMISSION_API_KEY ${CHARITY_COMMISSION_API_KEY}
cf set-env $TARGET_ENV CLAMAV_HTTP_PASS ${CLAMAV_HTTP_PASS}
cf set-env $TARGET_ENV CLAMAV_HTTP_USER ${CLAMAV_HTTP_USER}
cf set-env $TARGET_ENV S3_BUCKET_ARTIFACTS ${S3_BUCKET_ARTIFACTS}
cf set-env $TARGET_ENV S3_REGION ${S3_REGION}

# Set environment specific env vars
if [[ $ENV = "staging" ]]; then
  cf set-env $TARGET_ENV COMPANIES_HOUSE_API_KEY ${STAGING_COMPANIES_HOUSE_API_KEY}
  cf set-env $TARGET_ENV IDEAL_POSTCODES_API_KEY ${STAGING_IDEAL_POSTCODES_API_KEY}
  cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_KEY ${STAGING_PAR_GOVUK_NOTIFY_KEY}
  cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_TEMPLATE ${STAGING_PAR_GOVUK_NOTIFY_TEMPLATE}
  cf set-env $TARGET_ENV S3_ACCESS_KEY ${STAGING_S3_ACCESS_KEY}
  cf set-env $TARGET_ENV S3_BUCKET_PRIVATE ${STAGING_S3_BUCKET_PRIVATE}
  cf set-env $TARGET_ENV S3_BUCKET_PUBLIC ${STAGING_S3_BUCKET_PUBLIC}
  cf set-env $TARGET_ENV S3_SECRET_KEY ${STAGING_S3_SECRET_KEY}
  cf set-env $TARGET_ENV S3_BUCKET_ARTIFACTS ${STAGING_S3_BUCKET_ARTIFACTS}
fi

if [[ $ENV = "production" ]]; then
  cf set-env $TARGET_ENV COMPANIES_HOUSE_API_KEY ${PROD_COMPANIES_HOUSE_API_KEY}
  cf set-env $TARGET_ENV IDEAL_POSTCODES_API_KEY ${PROD_IDEAL_POSTCODES_API_KEY}
  cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_KEY ${PROD_PAR_GOVUK_NOTIFY_KEY}
  cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_TEMPLATE ${PROD_PAR_GOVUK_NOTIFY_TEMPLATE}
  cf set-env $TARGET_ENV S3_ACCESS_KEY ${PROD_S3_ACCESS_KEY}
  cf set-env $TARGET_ENV S3_BUCKET_PRIVATE ${PROD_S3_BUCKET_PRIVATE}
  cf set-env $TARGET_ENV S3_BUCKET_PUBLIC ${PROD_S3_BUCKET_PUBLIC}
  cf set-env $TARGET_ENV S3_SECRET_KEY ${PROD_S3_SECRET_KEY}
  cf set-env $TARGET_ENV S3_BUCKET_ARTIFACTS ${PROD_S3_BUCKET_ARTIFACTS}
fi

if [[ $ENV != "production" ]] || [[ $ENV != "staging" ]]; then
  cf set-env $TARGET_ENV COMPANIES_HOUSE_API_KEY ${NP_COMPANIES_HOUSE_API_KEY}
  cf set-env $TARGET_ENV IDEAL_POSTCODES_API_KEY ${NP_IDEAL_POSTCODES_API_KEY}
  cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_KEY ${NP_PAR_GOVUK_NOTIFY_KEY}
  cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_TEMPLATE ${NP_PAR_GOVUK_NOTIFY_TEMPLATE}
  cf set-env $TARGET_ENV S3_ACCESS_KEY ${NP_S3_ACCESS_KEY}
  cf set-env $TARGET_ENV S3_BUCKET_PRIVATE ${NP_S3_BUCKET_PRIVATE}
  cf set-env $TARGET_ENV S3_BUCKET_PUBLIC ${NP_S3_BUCKET_PUBLIC}
  cf set-env $TARGET_ENV S3_SECRET_KEY ${NP_S3_SECRET_KEY}
  cf set-env $TARGET_ENV S3_BUCKET_ARTIFACTS ${NP_S3_BUCKET_ARTIFACTS}
fi

####################################################################################
# Check for existing backing services and create if necessary
# This should only be done on non-production environments
# Any issues with production should be resolved manually
# For creating backing services see https://docs.cloud.service.gov.uk/deploying_services/
#
# `cf create-service` is by default asynchronous and requires
# polling to see if the tasks have been completed, for more information
# see https://github.com/cloudfoundry/cli/issues/1354
# Until then we're just going to wait for 10 minutes
# `cf bind-service` is likely to be made asynchronous in the future
####################################################################################
printf "Checking and enabling backing services...\n"

## Ensure the right service plan is selected
if [[ $ENV = "production" ]] || [[ $ENV = "staging" ]]; then
    PG_PLAN='medium-ha-13'
    REDIS_PLAN='medium-ha-6.x'
    OS_PLAN='small-ha-1'
else
    ## The free plan can be used for any non-critical environments
    PG_PLAN='small-13'
    REDIS_PLAN='tiny-6.x'
    OS_PLAN='tiny-1'
fi

# TODO Error happens here if there's an error during the creation of services.
# some services may not be in the correct state to tear down and so may not be removed.
# Catch any service errors and don't tear down until all services are ready.
if [[ $ENV != "production" ]]; then
    ## Check for the postgres database service
    if ! cf service $PG_BACKING_SERVICE 2>&1; then
        echo "################################################################################################"
        echo >&2 "The new postgres service is being created, this can take up to 10 minutes"
        echo "################################################################################################"

        printf "Creating postgres service, instance of $PG_PLAN...\n"
        cf create-service postgres $PG_PLAN $PG_BACKING_SERVICE -c '{"enable_extensions": ["citext","uuid-ossp","pg_trgm","pg_stat_statements"]}'

        ## If a new db is created it needs an import
        DB_RESET=y
    fi

    ## Check for the redis database service
    if ! cf service $REDIS_BACKING_SERVICE 2>&1; then
        echo "################################################################################################"
        echo >&2 "The new redis service is being created, this can take up to 10 minutes"
        echo "################################################################################################"

        printf "Creating redis service, instance of $REDIS_PLAN...\n"
        cf create-service redis $REDIS_PLAN $REDIS_BACKING_SERVICE
    fi

    ## Check for the opensearch service
    if ! cf service $OS_BACKING_SERVICE 2>&1; then
        echo "################################################################################################"
        echo >&2 "The new opensearch service is being created, this can take up to 10 minutes"
        echo "################################################################################################"

        printf "Creating opensearch service, instance of $OS_PLAN...\n"
        cf create-service opensearch $OS_PLAN $OS_BACKING_SERVICE
    fi

    ## Checking the postgres backing services
    cf_poll_service $PG_BACKING_SERVICE
    ## Checking the redis backing services
    cf_poll_service $REDIS_BACKING_SERVICE
    ## Checking the redis backing services
    cf_poll_service $OS_BACKING_SERVICE
fi

# Binding the postgres backing service
cf bind-service $TARGET_ENV $PG_BACKING_SERVICE
# Binding the redis backing service
cf bind-service $TARGET_ENV $REDIS_BACKING_SERVICE
# Binding the opensearch backing service
cf bind-service $TARGET_ENV $OS_BACKING_SERVICE
if [[ $ENV == "production" ]] && cf service $LOGGING_BACKING_SERVICE 2>&1; then
    # Binding the opss logging service
    cf bind-service $TARGET_ENV $LOGGING_BACKING_SERVICE
fi

## Deployment to no production environments need a database
if [[ $ENV != "production" ]] && [[ $DB_RESET == 'y' ]] && [[ ! -f $DB_IMPORT ]]; then
    printf "Non-production environments need a copy of the database to seed from at '$DB_IMPORT'.\n"
    exit 5
fi


####################################################################################
# Boot the app
####################################################################################
printf "Starting the application...\n"

cf start $TARGET_ENV

## Import the seed database and then delete it.
if [[ $ENV != "production" ]] && [[ $DB_RESET ]]; then
    if [[ ! -f "$BUILD_DIR/$DB_DIR/$DB_NAME.tar.gz" ]]; then
        printf "Seed database required, but could not find one at '$BUILD_DIR/$DB_DIR/sanitised-db.sql'.\n"
        exit 6
    fi

    # Running a python script instead of bash because python has immediate
    # access to all of the environment variables and configuration.
    printf "Importing the database...\n"
    cf run-task $TARGET_ENV -m 2G -k 2G --name DB_IMPORT -c "./scripts/drop.sh && \
        cd $REMOTE_BUILD_DIR/web && \
        tar --no-same-owner -zxvf $REMOTE_BUILD_DIR/$DB_DIR/$DB_NAME.tar.gz -C $REMOTE_BUILD_DIR/$DB_DIR && \
        drush @par.paas sql:cli < $REMOTE_BUILD_DIR/$DB_DIR/$DB_NAME.sql && \
        rm -f $REMOTE_BUILD_DIR/$DB_DIR/$DB_NAME.sql"

    # Wait for database to be imported.
    cf_poll_task $TARGET_ENV DB_IMPORT
    printf "Database imported...\n"

    printf "Sanitising PAR People Data...\n"
    cf run-task $TARGET_ENV -m 4G -k 4G --name SPP -c "./scripts/sanitise-par-people.sh"
    cf_poll_task $TARGET_ENV SPP
    printf "Sanitisation completed...\n"
fi

####################################################################################
# Blue-green deployment switch
####################################################################################
printf "Switching from green to blue...\n"

if [[ $ENV == "production" ]]; then
    CDN_DOMAIN="primary-authority.beis.gov.uk"
else
    CDN_DOMAIN=$ENV-cdn.par-beta.net
fi

cf map-route $TARGET_ENV cloudapps.digital -n beis-par-$ENV
if cf service $CDN_BACKING_SERVICE >/dev/null 2>&1; then
    cf map-route $TARGET_ENV $CDN_DOMAIN
fi


if [[ $ENV_ONLY != y ]]; then
    ## Only unmap blue routes if doing a blue-green deployment and it exists
    if cf app $BLUE_ENV >/dev/null 2>&1; then
        cf unmap-route $BLUE_ENV cloudapps.digital -n beis-par-$ENV

        ## Only unmap cdn service if doing a blue-green deployment and it exists
        if cf service $CDN_BACKING_SERVICE >/dev/null 2>&1; then
            cf unmap-route $BLUE_ENV $CDN_DOMAIN
        fi

        ## Only delete blue app if it exists and doing a blue-green deployment
        cf delete $BLUE_ENV -f
    fi

    ## Only rename green service if doing a blue-green deployment
    if cf app $TARGET_ENV >/dev/null 2>&1; then
        cf rename $TARGET_ENV $BLUE_ENV
    fi
    TARGET_ENV=$BLUE_ENV
fi

####################################################################################
# Scale up the application if required
####################################################################################
printf "Scaling up the application...\n"

if [[ $CF_INSTANCES -gt 1 ]]; then
    cf scale $TARGET_ENV -i $CF_INSTANCES
fi


####################################################################################
# Run post deployment scripts
####################################################################################
echo "################################################################################################"
echo >&2 "Deployment has been successfully deployed to 'https://$TARGET_ENV.cloudapps.digital'"
echo "################################################################################################"

printf "Running post deploy Drupal updates...\n"
cf run-task $TARGET_ENV -m 4G -k 4G --name POST_DEPLOY -c "./scripts/post-deploy.sh"
cf_poll_task $TARGET_ENV POST_DEPLOY
printf "Post deploy Drupal updates completed...\n"

printf "Running the remaining post deployment scripts...\n"

## Run cron to perform necessary startup tasks
cf run-task $TARGET_ENV -c "./scripts/cron-run.sh" -m 4G -k 4G --name CRON_RUNNER
cf_poll_task $TARGET_ENV CRON_RUNNER
printf "Cron completed...\n"

## Index the search engine
cf run-task $TARGET_ENV -c "./scripts/re-index.sh partnership_index --rebuild" -m 4G -k 4G --name SEARCH_REINDEX
cf_poll_task $TARGET_ENV SEARCH_REINDEX
printf "Search re-indexing complete...\n"

## Run the cache warmer asynchronously with lots of memory
cf run-task $TARGET_ENV -c "./scripts/cache-warmer.sh" -m 4G -k 4G --name CACHE_WARMER
cf_poll_task $TARGET_ENV CACHE_WARMER
printf "Cache warming complete...\n"
