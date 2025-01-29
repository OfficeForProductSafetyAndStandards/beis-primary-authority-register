#!/bin/bash
# This script will run cron in a given environment.
echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT
echo "Current working directory is ${PWD}"

../vendor/bin/drush cron
