#!/bin/bash
# This script will export a vault secret store to local environment variables.
# Usage: ./update-vault.sh secret-store

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

####################################################################################
# Set required parameters
#    SECRET_STORE (required) - the secret store to update
#    VAULT_ADDR - the vault service endpoint
#    VAULT_UNSEAL (required) - the key used to unseal the vault
#    VAULT_TOKEN (required) - the master token to unseal the vaule
####################################################################################
OPTIONS=v:u:t:
LONGOPTS=vault:,unseal:,token:

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
VAULT_ADDR=${VAULT_ADDR:="https://vault.primary-authority.services:8200"}
VAULT_UNSEAL=${VAULT_UNSEAL:-}
VAULT_TOKEN=${VAULT_TOKEN:-}

while true; do
    case "$1" in
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
    printf "Please specify the secret store to export...\n"
    exit 4
fi
SECRET=$1

####################################################################################
# Allow manual input of missing parameters
#    VAULT_TOKEN (required) - the vault token to unseal the vault
#    VAULT_UNSEAL (required) - the key used to unseal the vault
####################################################################################
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

vault operator unseal -tls-skip-verify $VAULT_UNSEAL

if [[ $(vault kv list -tls-skip-verify secret/par/env | awk 'NR > 2 {print $1}' | grep $SECRET) ]]; then
    VAULT_ENV=$SECRET
else
    VAULT_ENV='paas'
fi

## Set the environment variables by generating an .env file
printf "Using vault keystore: '$VAULT_ENV'...\n"

VAULT_VARS=($(vault kv get -tls-skip-verify secret/par/env/$VAULT_ENV | awk 'NR > 3 {print $1}'))
for VAR_NAME in "${VAULT_VARS[@]}"
do
  printf "$VAR_NAME='$(vault kv get --field=$VAR_NAME -tls-skip-verify secret/par/env/$VAULT_ENV)'\n" >> .env
done
## Export the vars in .env for use in this script
export $(egrep -v '^#' .env | xargs)

## Seal the vault now in case of an error
printf "Re-sealing vault...\n"
vault operator seal -tls-skip-verify
