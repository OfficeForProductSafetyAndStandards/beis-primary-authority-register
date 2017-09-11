#!/bin/bash

####################################################################################
# This script should be called from the root of the repository
#
# i.e. cf/push-continuous.sh
####################################################################################

cf push -f cf/manifests/manifest.continuous.yml

cf ssh par-beta-continuous -c "cd app/tools && python post_deploy.py"
