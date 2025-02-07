#!/bin/bash
echo $BASH_VERSION
set -o errexit -euo pipefail -o noclobber -o nounset

WEBROOT="${BASH_SOURCE%/*}/../web"
cd $WEBROOT
echo "Current working directory is ${PWD}"

drush spp
