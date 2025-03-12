#!/bin/bash
## For re-setting test data.
## Use as `./drupal-stub-data.sh`
echo $BASH_VERSION
set -o errexit -euo pipefail -o noclobber -o nounset

ROOT="${BASH_SOURCE%/*}/../web"
cd $ROOT
echo "Current working directory is ${PWD}"

# Enable the test content.
drush pm-uninstall par_data_test -y;
drush en par_data_test -y;
# Put the site in stubbed mode.
drush config-set par_data.settings stubbed true -y;
drush cache:rebuild;
