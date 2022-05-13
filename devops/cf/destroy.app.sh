#!/bin/bash
# This script will push local assets to an environment.
# Usage: ./destroy.app.sh $ENV

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset


####################################################################################
# Prerequisites - You'll need the following installed
#    Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html
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
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_PWD (required) - the password for the user account
####################################################################################
OPTIONS=u:p:
LONGOPTS=user:,password:

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
GOVUK_CF_USER=${GOVUK_CF_USER:-}
GOVUK_CF_PWD=${GOVUK_CF_PWD:-}

while true; do
    case "$1" in
        -u|--user)
            GOVUK_CF_USER="$2"
            shift 2
            ;;
        -p|--password)
            GOVUK_CF_PWD="$2"
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

## Block the removal of key environments.
if [[ $ENV == 'production' ]] || [[ $ENV == 'staging' ]]; then
    echo "You cannot remove $ENV environment."
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


####################################################################################
# Login to GovUK PaaS
####################################################################################
printf "Authenticating with GovUK PaaS...\n"

# Only allow apps in the development or staging space to be removed.
if [[ $ENV == 'staging' ]] || [[ $ENV =~ ^staging-.* ]]; then
    cf login -a api.cloud.service.gov.uk -u $GOVUK_CF_USER -p $GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-staging"
else
    cf login -a api.cloud.service.gov.uk -u $GOVUK_CF_USER -p $GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-development"
fi


####################################################################################
# Acquiring all the backing services that need to be removed.
####################################################################################
printf "Acquiring the backing services to be removed...\n"

APP="beis-par-$ENV"
CDN_BACKING_SERVICE="par-cdn-$ENV"
PG_BACKING_SERVICE="par-pg-$ENV"
REDIS_BACKING_SERVICE="par-redis-$ENV"
OS_BACKING_SERVICE="par-os-$ENV"


####################################################################################
# Unbinding all the backing services.
####################################################################################
printf "Removing cdn backing service $CDN_BACKING_SERVICE...\n"
if cf service $CDN_BACKING_SERVICE >/dev/null 2>&1; then
    if cf app $APP >/dev/null 2>&1; then
        printf "Unbinding cdn backing services...\n"
        cf unbind-service $APP $CDN_BACKING_SERVICE
    fi

    ## In some instances service keys may also have to be deleted
    if ! cf service-keys $CDN_BACKING_SERVICE | grep -v 'No service key for service instance'; then
          printf "Service keys will need to be deleted manually, see 'cf service-keys $CDN_BACKING_SERVICE'\n"
    fi

    cf delete-service -f $CDN_BACKING_SERVICE
fi

printf "Removing postgres backing service $PG_BACKING_SERVICE...\n"
if cf service $PG_BACKING_SERVICE >/dev/null 2>&1; then
    if cf app $APP >/dev/null 2>&1; then
        printf "Unbinding postgres backing services...\n"
        cf unbind-service $APP $PG_BACKING_SERVICE
    fi

    ## In some instances service keys may also have to be deleted
    if ! cf service-keys $PG_BACKING_SERVICE | grep -v 'No service key for service instance'; then
          printf "Service keys will need to be deleted manually, see 'cf service-keys $PG_BACKING_SERVICE'\n"
    fi

    cf delete-service -f $PG_BACKING_SERVICE
fi

printf "Removing redis backing service $REDIS_BACKING_SERVICE...\n"
if cf service $REDIS_BACKING_SERVICE >/dev/null 2>&1; then
    if cf app $APP >/dev/null 2>&1; then
        printf "Unbinding redis backing services...\n"
        cf unbind-service $APP $REDIS_BACKING_SERVICE
    fi

    ## In some instances service keys may also have to be deleted
    if ! cf service-keys $REDIS_BACKING_SERVICE | grep -v 'No service key for service instance'; then
          printf "Service keys will need to be deleted manually, see 'cf service-keys $REDIS_BACKING_SERVICE'\n"
    fi

    cf delete-service -f $REDIS_BACKING_SERVICE
fi

printf "Removing opensearch backing service $OS_BACKING_SERVICE...\n"
if cf service $OS_BACKING_SERVICE >/dev/null 2>&1; then
    if cf app $APP >/dev/null 2>&1; then
        printf "Unbinding opensearch backing services...\n"
        cf unbind-service $APP $OS_BACKING_SERVICE
    fi

    ## In some instances service keys may also have to be deleted
    if ! cf service-keys $OS_BACKING_SERVICE | grep -v 'No service key for service instance'; then
          printf "Service keys will need to be deleted manually, see 'cf service-keys $OS_BACKING_SERVICE'\n"
    fi

    cf delete-service -f $OS_BACKING_SERVICE
fi

## Remove the main app if it exists
printf "Removing the app '$APP'...\n"
if cf app $APP >/dev/null 2>&1; then
    cf delete -f $APP
fi

printf "Removing the routes...\n"
if cf app $APP >/dev/null 2>&1; then
    cf unmap-route $APP cloudapps.digital -n $APP
    cf unmap-route $APP cloudapps.digital -n $APP-green
fi
cf delete-route -f cloudapps.digital -n $APP
cf delete-route -f cloudapps.digital -n $APP-green
