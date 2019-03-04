#!/bin/bash
# This script will push local assets to an environment.
echo $BASH_VERSION

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

cf login -a api.cloud.service.gov.uk -u $GOVUK_CF_USER -p $GOVUK_CF_PWD

cf target -o office-for-product-safety-and-standards -s primary-authority-register


####################################################################################
# Backup Database to s3
####################################################################################
TARGET_ENV=par-beta-$ENV

if cf app $TARGET_ENV >/dev/null 2>&1; then
    cf ssh $TARGET_ENV -c "python app/devops/tools/postgres_dump.py"
fi
