#!/bin/bash
# This script will check the health of the search indexes.
echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

####################################################################################
# Set required parameters
#    INDEX (required) - the index to rebuild
####################################################################################
OPTIONS=r
LONGOPTS=reboot

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
REBOOT=${REBOOT:=n}

while true; do
    case "$1" in
        -r|--reboot)
            REBOOT=y
            shift
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
    echo "Please specify the index to check."
    exit 4
fi
INDEX=$1

# Process from the web root.
WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT
echo "Current working directory is ${PWD}"

# Check the health of a given search index.
../vendor/drush/drush/drush par-data:index-health partnership_index --index-health
