#!/bin/bash
# This script will push local assets to an environment.
echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset


####################################################################################
# Create polling function
# Used to check for the status of a PaaS service.
####################################################################################
function cf_poll {
    I=1
    printf "Waiting for $1 backing service...\n"
    while [[ $(cf service $1 | awk -F '  +' '/status:/ {print $2}' | grep 'in progress') ]]
    do
      printf "%0.s-" $(seq 1 $I)
      sleep 2
    done
}

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

command -v vault >/dev/null 2>&1 || {
    echo "################################################################################################"
    echo >&2 "Please install Vault CLI - https://www.vaultproject.io/docs/install/index.html"
    echo "################################################################################################"
    exit 1
}

command -v cf >/dev/null 2>&1 || {
    echo "################################################################################################"
    echo >&2 "Please install Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html"
    echo "################################################################################################"
    exit 1
}

####################################################################################
# Set required parameters
#    ENV (required) - the password for the user account
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_PWD (required) - the password for the user account
#    BUILD_DIR - the directory containing the build assets
#    VAULT_ADDR - the vault service endpoint
#    VAULT_UNSEAL_KEY (required) - the key used to unseal the vault
####################################################################################
OPTIONS=su:p:i:b:rd:v:u:t:x
LONGOPTS=single,user:,password:,instances:,database:,refresh-database,directory:,vault:,unseal:,token:,deploy-production

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
GOVUK_CF_USER=${GOVUK_CF_USER:-}
GOVUK_CF_PWD=${GOVUK_CF_PWD:-}
CF_INSTANCES=${CF_INSTANCES:=1}
DB_IMPORT=${DB_IMPORT:="$PWD/backups/sanitised-db.sql"}
DB_RESET=${DB_RESET:=n}
DEPLOY_PRODUCTION=${DEPLOY_PRODUCTION:=n}
BUILD_DIR=${BUILD_DIR:=$PWD}
VAULT_ADDR=${VAULT_ADDR:="https://vault.primary-authority.beis.gov.uk:8200"}
VAULT_UNSEAL=${VAULT_UNSEAL:-}
VAULT_TOKEN=${VAULT_TOKEN:-}

while true; do
    case "$1" in
        -s|--single)
            ENV_ONLY=y
            shift
            ;;
        -u|--user)
            GOVUK_CF_USER="$2"
            shift 2
            ;;
        -p|--password)
            GOVUK_CF_PWD="$2"
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
        -u|--unseal)
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
# Allow manual input of missing parameters
#    ENV (required) - the password for the user account
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_PWD (required) - the password for the user account
#    BUILD_DIR - the directory containing the build assets
#    VAULT_ADDR - the vault service endpoint
#    VAULT_UNSEAL_KEY (required) - the key used to unseal the vault
####################################################################################
if [[ -z "${GOVUK_CF_USER}" ]]; then
    echo -n "Enter your Cloud Foundry username: "
    read GOVUK_CF_USER
fi
if [[ -z "${GOVUK_CF_PWD}" ]]; then
    echo -n "Enter your Cloud Foundry password (will be hidden): "
    read -s GOVUK_CF_PWD
fi
if [[ -z "${VAULT_UNSEAL}" ]]; then
    echo -n "Enter your Vault unseal key (will be hidden): "
    read -s VAULT_UNSEAL
fi
if [[ -z "${VAULT_TOKEN}" ]]; then
    echo -n "Enter your Vault master token (will be hidden): "
    read -s VAULT_TOKEN
fi


####################################################################################
# Unseal the vault and read all variables in scope for this environment
# Vault is first sealed to ensure that deployment can't happen unless user has
# the unseal token.
####################################################################################
printf "Extracting Vault secrets...\n"

export VAULT_ADDR
export VAULT_TOKEN

vault operator seal -tls-skip-verify
vault operator unseal -tls-skip-verify $VAULT_UNSEAL

if [[ $(vault kv list -tls-skip-verify secret/par/env | awk 'NR > 2 {print $1}' | grep $ENV) ]]; then
    VAULT_ENV=$ENV
else
    VAULT_ENV='paas'
fi

## Ensure the production deployment uses the production vault keystore
if [[ $ENV == 'production' ]] && [[ $VAULT_ENV != $ENV ]]; then
    printf "Can't access the vault store for production secrets...\n"
    exit 12
fi

## Set the environment variables by generating an .env file
printf "Using vault keystore: '$VAULT_ENV'...\n"
rm -f .env
VAULT_VARS=($(vault kv get -tls-skip-verify secret/par/env/$VAULT_ENV | awk 'NR > 3 {print $1}'))
for VAR_NAME in "${VAULT_VARS[@]}"
do
  printf "$VAR_NAME='$(vault kv get --field=$VAR_NAME -tls-skip-verify secret/par/env/$VAULT_ENV)'\n" >> .env
done
## Export the vars in .env for use in this script
export $(egrep -v '^#' .env | xargs)

## Seal the vault now in case of an error
vault operator seal -tls-skip-verify


####################################################################################
# Login to GovUK PaaS
####################################################################################
printf "Authenticating with GovUK PaaS...\n"

cf login -a api.cloud.service.gov.uk -u $GOVUK_CF_USER -p $GOVUK_CF_PWD

if [[ $ENV == 'production' ]] || [[ $ENV == production-* ]]; then
    cf target -o "office-for-product-safety-and-standards" -s "primary-authority-register-production"
elif [[ $ENV == 'staging' ]] || [[ $ENV == staging-* ]]; then
    cf target -o "office-for-product-safety-and-standards" -s "primary-authority-register-staging"
else
    cf target -o "office-for-product-safety-and-standards" -s "primary-authority-register-development"
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

MANIFEST="${BASH_SOURCE%/*}/manifests/manifest.$ENV.yml"
if [[ ! -f $MANIFEST ]]; then
    MANIFEST="${BASH_SOURCE%/*}/manifests/manifest.non-production.yml"
fi

## Copy the seed database to the build directory to use for import
mkdir -p "$BUILD_DIR/backups"
if [[ -f $DB_IMPORT ]]; then
    cp $DB_IMPORT "$BUILD_DIR/backups/sanitised-db.sql"
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
            printf "If there are any service keys these will need to be deleted manually, see 'cf service-keys $PG_BACKING_SERVICE'\n"

            cf delete-service -f $REDIS_BACKING_SERVICE
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
# Used to check for the status of a PaaS service.
####################################################################################
function cf_poll {
    I=1
    printf "Waiting for $1 backing service...\n"
    while [[ $(cf service $1 | awk -F '  +' '/status:/ {print $2}' | grep 'in progress') ]]
    do
      printf "%0.s-" $(seq 1 $I)
      sleep 2
    done
    printf "Backing service $1 is running...\n"
}

####################################################################################
# Waiting for cloud foundry to be ready
# If an existing process is already in progress for this environment then wait
# for it's completion before continuing.
####################################################################################
printf "Waiting for cloud foundry (readiness)...\n"

## Checking the app
printf "Waiting for the app...\n"
I=1
while [[ $(cf app $TARGET_ENV | awk -F '  +' '/status:/ {print $2}' | grep 'in progress') ]]
do
  printf "%0.s-" $(seq 1 $I)
  sleep 2
done

## Checking the postgres backing services
cf_poll $PG_BACKING_SERVICE
## Checking the redis backing services
cf_poll $REDIS_BACKING_SERVICE


####################################################################################
# Start the app
# And set the environment variables. Even though php will read from the .env file
# setting the cf variables directly allows them to be accessed by other scripts
# see https://docs.cloud.service.gov.uk/deploying_apps.html#deploying-an-app
####################################################################################
printf "Pushing the application...\n"

cf push --no-start -f $MANIFEST -p $BUILD_DIR -n $TARGET_ENV $TARGET_ENV

## Set the cf environment variables directly
for VAR_NAME in "${VAULT_VARS[@]}"
do
    cf set-env $TARGET_ENV $VAR_NAME ${!VAR_NAME} > /dev/null
done
cf set-env $TARGET_ENV APP_ENV $ENV


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
    PG_PLAN='medium-ha-9.5'
    REDIS_PLAN='medium-ha-3.2'
else
    ## The free plan can be used for any non-critical environments
    PG_PLAN='tiny-unencrypted-9.5'
    REDIS_PLAN='tiny-3.2'
fi

if [[ $ENV != "production" ]]; then
    ## Check for the postgres database service
    if ! cf service $PG_BACKING_SERVICE 2>&1; then
        printf "Creating postgres service, instance of $PG_PLAN...\n"
        cf create-service postgres $PG_PLAN $PG_BACKING_SERVICE

        echo "################################################################################################"
        echo >&2 "The new postgres service is being created, this can take up to 10 minutes"
        echo "################################################################################################"

        ## If a new db is created it needs an import
        DB_RESET=y
    fi

    ## Check for the redis database service
    if ! cf service $REDIS_BACKING_SERVICE 2>&1; then
        printf "Creating redis service, instance of $PG_PLAN...\n"
        cf create-service redis $REDIS_PLAN $REDIS_BACKING_SERVICE

        echo "################################################################################################"
        echo >&2 "The new redis service is being created, this can take up to 10 minutes"
        echo "################################################################################################"
    fi

    ## Checking the postgres backing services
    cf_poll $PG_BACKING_SERVICE
    ## Checking the redis backing services
    cf_poll $REDIS_BACKING_SERVICE

    # Binding the postgres backing service
    cf bind-service $TARGET_ENV $PG_BACKING_SERVICE
    # Binding the redis backing service
    cf bind-service $TARGET_ENV $REDIS_BACKING_SERVICE

    ## Deployment to no production environments need a database
    if [[ $DB_RESET == 'y' ]] && [[ ! -f $DB_IMPORT ]]; then
        printf "Non-production environments need a copy of the database to seed from at '$DB_IMPORT'.\n"
        exit 5
    fi
fi


####################################################################################
# Boot the app
####################################################################################
printf "Starting the application...\n"

cf start $TARGET_ENV

## Import the seed database and then delete it.
if [[ $ENV != "production" ]] && [[ $DB_RESET ]]; then
    if [[ ! -f "$BUILD_DIR/backups/sanitised-db.sql" ]]; then
        printf "Seed database required, but could not find one at '$BUILD_DIR/backups/sanitised-db.sql'.\n"
        exit 6
    fi

    cf ssh $TARGET_ENV -c "cd app && python ./devops/tools/import_fresh_db.py -f ./backups/sanitised-db.sql && rm -f ./backups/sanitised-db.sql"
fi

cf ssh $TARGET_ENV -c "cd app && python ./devops/tools/post_deploy.py"


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

if [[ CF_INSTANCES -gt 1 ]]; then
    cf scale $TARGET_ENV -i CF_INSTANCES
fi


####################################################################################
# Run post deployment scripts
####################################################################################
echo "################################################################################################"
echo >&2 "Deployment has been successfully deployed to 'https://$TARGET_ENV.cloudapps.digital'"
echo "################################################################################################"

printf "Running the post deployment scripts...\n"

cf ssh $TARGET_ENV -c "cd app/devops/tools && python cron_runner.py"
cf ssh $TARGET_ENV -c "cd app/devops/tools && python cache_warmer.py"
