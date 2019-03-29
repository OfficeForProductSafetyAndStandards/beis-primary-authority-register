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


####################################################################################
# Set required parameters
#    ENV (required) - the password for the user account
#    GOVUK_CF_USER (required) - the user deploying the script
#    GOVUK_CF_PWD (required) - the password for the user account
#    BUILD_DIR - the directory containing the build assets
#    VAULT_ADDR - the vault service endpoint
#    VAULT_UNSEAL_KEY (required) - the key used to unseal the vault
####################################################################################
OPTIONS=pd:a:
LONGOPTS=push,directory:,alias:

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

while true; do
    case "$1" in
        -p|--push)
            AWS_PUSH=y
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
# Backup Database to s3
####################################################################################
WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT
printf "Running export as: $USER\n"
printf "Current working directory: $PWD\n"

../vendor/drush/drush/drush $DRUPAL_ALIAS status

FILE_NAME="db-dump-$NAME-unsanitized"
DATE=$(date +%Y-%m-%d)

mkdir -p $DIRECTORY
rm -f $DIRECTORY/$FILE_NAME.sql

printf "Exporting database dump...\n"
printf "Running: drush $DRUPAL_ALIAS sql-dump --result-file='$DIRECTORY/$FILE_NAME.sql' --extra='-O -x'\n"
../vendor/drush/drush/drush $DRUPAL_ALIAS sql-dump --result-file="$DIRECTORY/$FILE_NAME.sql" --extra="-O -x" --verbose --debug

printf "Packaging database dump...\n"
tar -zcvf $DIRECTORY/$FILE_NAME-latest.tar.gz -C $DIRECTORY "$FILE_NAME.sql"
tar -zcvf $DIRECTORY/$FILE_NAME-$DATE.tar.gz -C $DIRECTORY "$FILE_NAME.sql"

if [[ $AWS_PUSH == y ]]; then
    printf "Uploading database archives...\n"
    ../vendor/drush/drush/drush fsp s3backups $DIRECTORY/$FILE_NAME-latest.tar.gz $FILE_NAME-latest.tar.gz
    ../vendor/drush/drush/drush fsp s3backups $DIRECTORY/$FILE_NAME-$DATE.tar.gz $FILE_NAME-$DATE.tar.gz
fi

printf "Database archive completed...\n"
