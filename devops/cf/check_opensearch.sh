#!/bin/bash
# This script will check the health of a given opensearch server.
# Usage: ./check_opensearch.sh -i partnership_index $ENV

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
#    ENV (required) - the environment to check for
#    BUILD_VER (optional) - the build tag being pushed
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_PWD (required) - the password for the user account
#    BUILD_DIR - the directory containing the build assets
#    VAULT_ADDR - the vault service endpoint
#    VAULT_UNSEAL_KEY (required) - the key used to unseal the vault
####################################################################################
OPTIONS=i:l:u:p:
LONGOPTS=index,local-port:,user:,password:

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
INDEX=${INDEX:="partnership_index"}
LOCAL_PORT=${LOCAL_PORT:="4430"}

while true; do
    case "$1" in
        -i|--index)
            INDEX="$2"
            shift 2
            ;;
        -l|--local-port)
            LOCAL_PORT="$2"
            shift 2
            ;;
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


####################################################################################
# Allow manual input of missing parameters
#    ENV (required) - the password for the user account
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_PWD (required) - the password for the user account
#    INDEX - the index to check the health for
####################################################################################
if [[ -z "${GOVUK_CF_USER}" ]]; then
    echo -n "Enter your Cloud Foundry username: "
    read GOVUK_CF_USER
fi
if [[ -z "${GOVUK_CF_PWD}" ]]; then
    echo -n "Enter your Cloud Foundry password (will be hidden): "
    read -s GOVUK_CF_PWD
fi
if [[ -z "${INDEX}" ]]; then
    echo -n "Please choose an index to check: "
    read -s INDEX
fi


####################################################################################
# Login to GovUK PaaS
####################################################################################
printf "Authenticating with GovUK PaaS...\n"

if [[ $ENV == 'production' ]] || [[ $ENV == production-* ]]; then
    cf login -a api.cloud.service.gov.uk -u $GOVUK_CF_USER -p $GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-production"
elif [[ $ENV == 'staging' ]] || [[ $ENV == staging-* ]]; then
    cf login -a api.cloud.service.gov.uk -u $GOVUK_CF_USER -p $GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-staging"
else
    cf login -a api.cloud.service.gov.uk -u $GOVUK_CF_USER -p $GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-development"
fi


####################################################################################
# Configure the application
# Some values van be set based on the variables provided to this script
# others need to be provided in the form of an app manifest
# To understand manifest configuration see https://docs.cloudfoundry.org/devguide/deploy-apps/manifest.html
####################################################################################
printf "Configuring the application...\n"

APP="beis-par-$ENV"
OS_BACKING_SERVICE="par-os-$ENV"


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

## Checking the opensearch backing services
cf_poll $OS_BACKING_SERVICE


####################################################################################
# Connecting to the opensearch backing service.
####################################################################################
printf "Connecting to the opensearch backing service...\n"

cf create-service-key $OS_BACKING_SERVICE health-check-creds

# Remove the first line, which describes the command.
# https://docs.cloudfoundry.org/devguide/services/service-keys.html#get-credentials-for-a-service-key
SERVICE_CREDENTIALS=`cf service-key $OS_BACKING_SERVICE health-check-creds | sed 1d`

SERVER_URL=`echo $SERVICE_CREDENTIALS | jq -r '.uri'`
SERVER_PORT=`echo $SERVICE_CREDENTIALS | jq -r '.port'`
SERVER_HOSTNAME=`echo $SERVICE_CREDENTIALS | jq -r '.hostname'`
SERVER_USER=`echo $SERVICE_CREDENTIALS | jq -r '.username'`
SERVER_PWD=`echo $SERVICE_CREDENTIALS | jq -r '.password'`

# More work needed to create an appropriate tunnel in the background.
SSH_ENDPOINT=`cf curl /v2/info | jq -r '.app_ssh_endpoint'`
echo $SSH_ENDPOINT

ssh -o ExitOnForwardFailure=yes -f -N -L $LOCAL_PORT:"cf:APP-GUID/APP-INSTANCE-INDEX@$SSH_ENDPOINT" user@$ip

cf ssh -L $LOCAL_PORT:$SERVER_HOSTNAME:$SERVER_PORT $APP &
SSH_PID=$!

####################################################################################
# Checking the health of the opensearch backing service.
####################################################################################
printf "Checking the health of the opensearch backing service...\n"

curl -u "$SERVER_USER:$SERVER_PWD" -k -X GET \
    "https://localhost:$LOCAL_PORT/"

# Check the cluster health.
SERVER_HEALTH=`curl -u "$SERVER_USER:$SERVER_PWD" -s -k -X GET \
    "https://localhost:4431/_cluster/health?wait_for_status=yellow&timeout=50s"`

SERVER_STATUS=`echo $SERVER_HEALTH | jq -r ".status"`
printf "Cluster health is '$SERVER_STATUS'.\n"
if [[ $SERVER_STATUS == "green" ]]; then
    exit 10
fi

# Check the index health.
INDEX_HEALTH=`curl -u "$SERVER_USER:$SERVER_PWD" -s -k -X GET \
    "https://localhost:$LOCAL_PORT/$INDEX/_stats/docs"`

#INDEX_COUNT=`echo $INDEX_HEALTH | jq -r ".indices.$SERVER.total.docs.count"`
#if [[ $INDEX_COUNT != "green" ]]; then
#    printf "Cluster health is '$SERVER_STATUS'.\n"
#    exit 11
#fi


####################################################################################
# disconnecting from the opensearch backing service.
####################################################################################
printf "Disconnecting from the opensearch backing service...\n"
kill $SSH_PID
