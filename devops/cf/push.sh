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
#    AWS_KEY (required) - the user deploying the script
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_PWD (required) - the password for the user account
#    BUILD_DIR - the directory containing the build assets
#    VAULT_ADDR - the vault service endpoint
#    VAULT_UNSEAL_KEY (required) - the key used to unseal the vault
####################################################################################
OPTIONS=z:su:p:i:b:rd:v:u:t:
LONGOPTS=version:,single,user:,password:,instances:,database:,refresh-database,directory:,vault:,unseal:,token:

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
VERSION=${VERSION:-}
ENV_ONLY=${ENV_ONLY:=n}
GOVUK_CF_USER=${GOVUK_CF_USER:-}
GOVUK_CF_PWD=${GOVUK_CF_PWD:-}
CF_INSTANCES=${CF_INSTANCES:=1}
DB_IMPORT=${DB_IMPORT:="$PWD/backups/sanitised-db.sql"}
DB_RESET=${DB_RESET:=n}
BUILD_DIR=${BUILD_DIR:=$PWD}
VAULT_ADDR=${VAULT_ADDR:="https://vault.primary-authority.beis.gov.uk:8200"}
VAULT_UNSEAL=${VAULT_UNSEAL:-}
VAULT_TOKEN=${VAULT_TOKEN:-}

while true; do
    case "$1" in
        -z|--version)
            VERSION="$2"
            shift 2
            ;;
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
if [[ -z "${VERSION}" ]]; then
    echo -n "Enter the tag you wish to deploy: "
    read GOVUK_CF_USER
fi
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

export AWS_ACCESS_KEY_ID=`vault read -tls-skip-verify -field=AWS_ACCESS_KEY_ID secret/par/deploy/aws`
export AWS_DEFAULT_REGION=`vault read -tls-skip-verify -field=AWS_REGION secret/par/deploy/aws`
export AWS_SECRET_ACCESS_KEY=`vault read -tls-skip-verify -field=AWS_SECRET_ACCESS_KEY secret/par/deploy/aws`

## Seal the vault now in case of an error
vault operator seal -tls-skip-verify


####################################################################################
# Pull the packaged version from S3
####################################################################################
printf "Pulling version $VERSION...\n"
    
mkdir -p $BUILD_DIR
rm -rf $BUILD_DIR/*

printf "Downloading build package s3://beis-par-artifacts/builds/$VERSION.tar.gz...\n"
aws s3 cp s3://beis-par-artifacts/builds/$VERSION.tar.gz /tmp/
    
## Check that we got the package
if [ ! -f /tmp/$VERSION.tar.gz ]; then
   exit
fi
    
## Unpack and remove package file
printf "Extracting the tar ball to $BUILD_DIR...\n"
tar -zxvf /tmp/$VERSION.tar.gz -C $BUILD_DIR > /dev/null 2>&1
rm /tmp/$VERSION.tar.gz
    
####################################################################################
# Run the deployment script
####################################################################################
SCRIPT="${BASH_SOURCE%/*}/push.local.sh -d $BUILD_DIR $ENV"

printf "Tag $VERSION has been pulled...\n"
printf "Please deploy this to an environment using the following command...\n"
printf "$SCRIPT\n"