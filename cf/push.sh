#!/bin/bash

####################################################################################
# To use this script, set up a Python virtual environment
# and set your AWS access keys
####################################################################################
# mkvirtualenv beis-par-beta
# workon beis-par-beta
# pip install --upgrade awscli
# aws --version
####################################################################################
# You'll need to be logged into:
#
#    Gov.uk PaaS
#       CLI: https://docs.cloudfoundry.org/cf-cli/install-go-cli.html

#       cf login -a api.cloud.service.gov.uk -u <USERNAME>
#           
#    Par Beta Vault
        CLI: https://www.vaultproject.io/docs/install/index.html
        
#       vault auth
#       vault unseal
####################################################################################

ENV=$1
VER=$2

AWS_ACCESS_KEY_ID=`vault read -field=AWS_ACCESS_KEY_ID secret/par/deploy/aws`
AWS_SECRET_ACCESS_KEY=`vault read -field=AWS_SECRET_ACCESS_KEY secret/par/deploy/aws`
S3_ACCESS_KEY=`vault read -field=S3_ACCESS_KEY secret/par/env/$ENV`
S3_SECRET_KEY=`vault read -field=S3_SECRET_KEY secret/par/env/$ENV`
PAR_HASH_SALT=`vault read -field=PAR_HASH_SALT secret/par/env/$ENV`
S3_BUCKET_PUBLIC=`vault read -field=S3_BUCKET_PUBLIC secret/par/env/$ENV`
S3_BUCKET_PRIVATE=`vault read -field=S3_BUCKET_PRIVATE secret/par/env/$ENV`
S3_BUCKET_ARTIFACTS=`vault read -field=S3_BUCKET_ARTIFACTS secret/par/env/$ENV`
PAR_GOVUK_NOTIFY_KEY=`vault read -field=PAR_GOVUK_NOTIFY_KEY secret/par/env/$ENV`
PAR_GOVUK_NOTIFY_TEMPLATE=`vault read -field=PAR_GOVUK_NOTIFY_TEMPLATE secret/par/env/$ENV`

vault seal

# We are in the /cf directory

if [ "$VER" != "" ]; then

    source .env.$ENV
    
    echo "Pulling version $VER"
    rm -rf build
    mkdir build
    
    cd build
    
    aws s3 cp s3://transform-par-beta-artifacts/builds/$VER.tar.gz .
    
    if [ ! -f $VER.tar.gz ]; then
       exit
    fi
    
    tar -zxvf $VER.tar.gz
    rm $VER.tar.gz
    
    # Stay in the build directory to push the unpacked code
else
    # We need to push from the root directory
    cd ..
fi

cf push -f manifest.$ENV.yml

cf set-env par-beta-$ENV S3_ACCESS_KEY $S3_ACCESS_KEY
cf set-env par-beta-$ENV S3_SECRET_KEY $S3_SECRET_KEY
cf set-env par-beta-$ENV PAR_HASH_SALT $PAR_HASH_SALT
cf set-env par-beta-$ENV S3_BUCKET_PUBLIC $S3_BUCKET_PUBLIC
cf set-env par-beta-$ENV S3_BUCKET_PRIVATE $S3_BUCKET_PRIVATE
cf set-env par-beta-$ENV S3_BUCKET_ARTIFACTS $S3_BUCKET_ARTIFACTS
cf set-env par-beta-$ENV APP_ENV $ENV
cf set-env par-beta-$ENV PAR_GOVUK_NOTIFY_KEY $PAR_GOVUK_NOTIFY_KEY
cf set-env par-beta-$ENV PAR_GOVUK_NOTIFY_TEMPLATE $PAR_GOVUK_NOTIFY_TEMPLATE

cf restage par-beta-$ENV

cf ssh par-beta-$ENV -c "cd app/tools && python post_deploy.py"

if [ "$VER" != "" ]; then
    # For packaged code, go back to the /cf directory to set the domain, if any
    cd ..
    sh update-domain-$ENV.sh
fi

