#!/bin/bash

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT

echo "Current working directory is ${PWD}"

drush pcw par_data_partnership
drush pcw par_data_authority
drush pcw par_data_organisation
drush pcw par_data_enforcement_notice
drush pcw par_data_enforcement_action
drush pcw par_data_general_enquiry
drush pcw par_data_person
