#!/bin/bash
# This script will push local assets to an environment.
echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

####################################################################################
# Prerequisites - You'll need the following installed
#    AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html
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
#    PROD_GOVUK_CF_USER (required) - the user deploying the script
#    PROD_GOVUK_CF_PWD (required) - the password for the user account
#    BUILD_DIR - the directory containing the build assets
#    VAULT_ADDR - the vault service endpoint
#    VAULT_UNSEAL_KEY (required) - the key used to unseal the vault
####################################################################################
OPTIONS=bsd:a:u:p:
LONGOPTS=backup,sanitised,directory:,alias:,user:,password:

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
AWS_PUSH=${AWS_PUSH:=n}
DIRECTORY=${DIRECTORY:="/tmp"}
DRUPAL_ALIAS=${DRUPAL_ALIAS:="@par.paas"}
DEV_GOVUK_CF_USER=${DEV_GOVUK_CF_USER:-}
DEV_GOVUK_CF_PWD=${DEV_GOVUK_CF_PWD:-}

# Set the defaults.
SANITISED=unsanitised

while true; do
    case "$1" in
        -b|--backup)
            AWS_PUSH=y
            shift
            ;;
        -s|--sanitised)
            SANITISED=sanitised
            shift
            ;;
        -d|--directory)
            DIRECTORY="$2"
            shift 2
            ;;
        -a|--alias)
            DRUPAL_ALIAS="$2"
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
    echo "Please specify the name for this database backup."
    exit 4
fi
NAME=$1


####################################################################################
# Allow manual input of missing parameters
#    DEV_GOVUK_CF_USER (required) - the user deploying the script
#    DEV_GOVUK_CF_PWD (required) - the password for the user account
####################################################################################
if [[ -z "${DEV_GOVUK_CF_USER}" ]]; then
    echo -n "Enter your Cloud Foundry username: "
    read DEV_GOVUK_CF_USER
fi
if [[ -z "${DEV_GOVUK_CF_PWD}" ]]; then
    echo -n "Enter your Cloud Foundry password (will be hidden): "
    read -s DEV_GOVUK_CF_PWD
fi


####################################################################################
# Login to GovUK PaaS
####################################################################################
printf "Authenticating with GovUK PaaS...\n"

if [[ $NAME == 'production' ]] || [[ $NAME == production-* ]]; then
    cf login -a api.cloud.service.gov.uk -u $DEV_GOVUK_CF_USER -p $DEV_GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-production"
elif [[ $NAME == 'staging' ]] || [[ $NAME == staging-* ]]; then
    cf login -a api.cloud.service.gov.uk -u $DEV_GOVUK_CF_USER -p $DEV_GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-staging"
else
    cf login -a api.cloud.service.gov.uk -u $DEV_GOVUK_CF_USER -p $DEV_GOVUK_CF_PWD \
      -o "office-for-product-safety-and-standards" -s "primary-authority-register-development"
fi


####################################################################################
# Backup Database to s3
####################################################################################
WEBROOT="${BASH_SOURCE%/*}/.."
cd $WEBROOT
printf "Current working directory: $PWD\n"

SERVICE_NAME="par-pg-$NAME"
FILE_NAME="db-dump-$NAME-$SANITISED"
DATE=$(date +%Y-%m-%d)

mkdir -p $DIRECTORY
rm -f $DIRECTORY/$FILE_NAME.sql

printf "Exporting database dump...\n"

# Select the tables to exclude from the dump entirely.
skip_tables=(
  migration_*
  test*
  watchdog
  webprofiler
)
# Select the tables to only dump the schema for, ignoring all content.
structure_tables=(
  cache
  cache_*
  history
  search_*
  sessions
  flood
)

# Set the dump arguments
dump_args=(
  -f "$DIRECTORY/${FILE_NAME}.sql"
  --verbose
  --clean
  --no-owner
  --no-acl
)
for t in ${skip_tables[@]}; do
  dump_args+=(--exclude-table 'public.'$t)
done
for t in ${structure_tables[@]}; do
  dump_args+=(--exclude-table-data 'public.'$t)
done

# Using `pg_dump` instead of `drush sql:dump` @see https://regulatorydelivery.atlassian.net/browse/PAR-1699
# postgres client binary no longer available to drush in deployed app.
echo -e "Piping command: cf conduit $SERVICE_NAME -c '{\"read_only\": true}'"
echo -e "Running: pg_dump ${dump_args[@]}"
cf conduit $SERVICE_NAME -c '{"read_only": true}' -- \
  pg_dump "${dump_args[@]}"

## Remove erroneous pg_dump roles applied despite --no-owner flags.
## https://stackoverflow.com/questions/60605491/using-pg-dump-with-no-owner-information-still-outputting-role-information
#sed -e '/-- do not execute if not member of manager role/,+4d' $DIRECTORY/${FILE_NAME}_raw.sql > $DIRECTORY/$FILE_NAME.sql

printf "Packaging database dump...\n"
tar -zcvf $DIRECTORY/$FILE_NAME-latest.tar.gz -C $DIRECTORY "$FILE_NAME.sql"
tar -zcvf $DIRECTORY/$FILE_NAME-$DATE.tar.gz -C $DIRECTORY "$FILE_NAME.sql"

printf "Database archive completed...\n"
