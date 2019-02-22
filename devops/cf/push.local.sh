#!/bin/bash
# This script will push local assets to an environment.

set -o errexit -euo pipefail -o noclobber -o nounset

####################################################################################
# Prerequisites - You'll need the following installed
#    AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html
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

command -v aws >/dev/null 2>&1 || {
    echo "################################################################################################"
    echo >&2 "Please install AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html"
    echo "If you set it up in a Python virtual env, you may need to run workon"
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
OPTIONS=se:u:p:d:v:u:t:
LONGOPTS=single,environment:,user:,password:,directory:,vault:,unseal:,token:

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
BUILD_DIR=${BUILD_DIR:=$PWD}
VAULT_ADDR=${VAULT_ADDR:=https://vault.primary-authority.beis.gov.uk:8200}
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

## Deployment to production environment isn't supported at this time
if [[ $ENV == 'production' ]]; then
    echo "Deployment to production isn't supported at this time."
    exit 11
fi

## Deployment to no production environments need a database
if [[ $ENV != "production" ]] && [[ ! -f "$BUILD_DIR/backups/sanitised-db.sql" ]]; then
    printf "Non-production environments need a copy of the database to seed from at '$BUILD_DIR/backups/sanitised-db.sql'.\n"
    exit 5
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

## Set the environment variables by generating an .env file
rm -f .env
VAULT_VARS=($(vault kv get -tls-skip-verify secret/par/env/staging | awk 'NR > 3 {print $1}'))
for VAR_NAME in "${VAULT_VARS[@]}"
do
  printf "$VAR_NAME='$(vault kv get --field=$VAR_NAME -tls-skip-verify secret/par/env/staging)'\n" >> .env
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

cf target -o office-for-product-safety-and-standards -s primary-authority-register


####################################################################################
# Configure the application
# Some values van be set based on the variables provided to this script
# others need to be provided in the form of an app manifest
# To understand manifest configuration see https://docs.cloudfoundry.org/devguide/deploy-apps/manifest.html
####################################################################################
printf "Configuring the application...\n"

if [[ $ENV_ONLY == y ]]; then
    TARGET_ENV=par-beta-$ENV
else
    TARGET_ENV=par-beta-$ENV-green
fi

MANIFEST="${BASH_SOURCE%/*}/manifests/manifest.$ENV.yml"
if [[ ! -f $MANIFEST ]]; then
    MANIFEST="${BASH_SOURCE%/*}/manifests/manifest.non-production.yml"
fi


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
    cf set-env $TARGET_ENV $VAR_NAME ${!VAR_NAME}
done
cf set-env $TARGET_ENV APP_ENV $ENV


####################################################################################
# Check for existing backing services and create if necessary
# This should only be done on non-production environments
# Any issues with production should be resolved manually
# For creating backing services see https://docs.cloud.service.gov.uk/deploying_services/
####################################################################################
printf "Checking and enabling backing services...\n"

if [[ $ENV != "production" ]]; then

    ## Check for the postgres database service
    if ! cf service par-pg-$ENV 2>&1; then
        printf "Creating postgres service, instance of tiny-unencrypted-9.5 (free)...\n"
        cf create-service postgres tiny-unencrypted-9.5 par-pg-$ENV
        cf bind-service $TARGET_ENV par-pg-$ENV
        IMPORT_DB=true
    fi

    ## Check for the cdn service
#    if ! cf service par-cdn-$ENV 2>&1 1>/dev/null; then
#        printf "Creating cdn service, instance of cdn-route...\n"
#        cf create-service cdn-route cdn-route par-cdn-$ENV
#
#        cf create-domain beis-nmo-trial $ENV-cdn.par-beta.net
#        cf map-route par-beta-$ENV $ENV-cdn.par-beta.net
#        cf create-service cdn-route cdn-route par-cdn-$ENV -c '{"domain":"'$ENV'-cdn.par-beta.net"}'
#    fi
fi


####################################################################################
# Start the app
####################################################################################
printf "Starting the application...\n"

cf start $TARGET_ENV

if [[ $ENV != "production" ]] && [[ ! -z IMPORT_DB ]]; then
    cf ssh $TARGET_ENV -c "cd app && python ./devops/tools/import_fresh_db.py -f ./backups/sanitised-db.sql"
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

if [[ $ENV_ONLY != y ]]; then
    cf map-route $TARGET_ENV cloudapps.digital -n par-beta-$ENV
    cf unmap-route par-beta-$ENV cloudapps.digital -n par-beta-$ENV
    cf map-route $TARGET_ENV $CDN_DOMAIN
    cf unmap-route par-beta-$ENV $CDN_DOMAIN

    ## Only delete blue if it exists
    if ! cf app par-beta-$ENV 2>&1; then
        cf delete par-beta-$ENV -f
    fi

    cf rename $TARGET_ENV par-beta-$ENV
    TARGET_ENV=par-beta-$ENV
fi


####################################################################################
# Scale up the application if required
####################################################################################
echo -n "Scaling up the application...\n"

if [[ CF_INSTANCES -gt 1 ]]; then
    cf scale par-beta-$ENV -i CF_INSTANCES
fi


####################################################################################
# Run post deployment scripts
####################################################################################
printf "Running the post deployment scripts...\n"

cf ssh $TARGET_ENV -c "cd app/devops/tools && python cron_runner.py"
cf ssh $TARGET_ENV -c "cd app/devops/tools && python cache_warmer.py"
