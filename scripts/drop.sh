#!/bin/bash
## For dropping a database.
## Use as `./drop.sh`
echo $BASH_VERSION
set -o errexit -euo pipefail -o noclobber -o nounset

ROOT="${BASH_SOURCE%/*}/../web"
cd $ROOT
echo "Current working directory is ${PWD}"

# Set default drush alias.
echo "Dropping the database..."
../vendor/bin/drush sql:drop -y;

