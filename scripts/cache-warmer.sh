#!/bin/bash
# This script will push local assets to an environment.
echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT
echo "Current working directory is ${PWD}"

../vendor/bin/drush pcw par_data_partnership
../vendor/bin/drush pcw par_data_authority
../vendor/bin/drush pcw par_data_organisation
../vendor/bin/drush pcw par_data_enforcement_notice
../vendor/bin/drush pcw par_data_enforcement_action
../vendor/bin/drush pcw par_data_general_enquiry
../vendor/bin/drush pcw par_data_person
