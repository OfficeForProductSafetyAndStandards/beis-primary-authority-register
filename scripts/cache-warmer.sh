#!/bin/bash
# This script will push local assets to an environment.
echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT

../vendor/drush/drush/drush pcw par_data_partnership
../vendor/drush/drush/drush pcw par_data_authority
../vendor/drush/drush/drush pcw par_data_organisation
../vendor/drush/drush/drush pcw par_data_enforcement_notice
../vendor/drush/drush/drush pcw par_data_enforcement_action
../vendor/drush/drush/drush pcw par_data_general_enquiry
../vendor/drush/drush/drush pcw par_data_person
