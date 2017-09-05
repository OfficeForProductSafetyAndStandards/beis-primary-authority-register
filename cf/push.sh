#!/bin/bash

####################################################################################
# Run this script from the /cf directory of the repository
# Users will be prompted for:
#       Target environent
#       Build version
#       Vault unseal key
####################################################################################
# You'll need the following installed
#
#    AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html
#    Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html
#    Vault CLI - https://www.vaultproject.io/docs/install/index.html
####################################################################################
#----------------------
# Log into Gov.uk Paas |
#----------------------
#     cf login -a api.cloud.service.gov.uk -u <USERNAME>
#
#--------------------------------------           
# Add the following to your hosts file |
#--------------------------------------
#     35.176.189.183 vault.primary-authority.beis.gov.uk
#
# Access is restricted to members of the TransformCore GitHub organisation. 
# Generate a GitHub Personal Access Token, which will be requested
# during "vault auth"
#
#----------------
# Log into Vault |
#----------------
#     export VAULT_ADDR=https://vault.primary-authority.beis.gov.uk:8200
#     vault auth
####################################################################################

CURRENT_DIR=${PWD##*/}

if [ $CURRENT_DIR != "cf" ]; then
    echo "####################################################################################"
    echo >&2 "Please run this script from the /cf directory of the repository"
    echo "####################################################################################"
    exit 1
fi

command -v vault >/dev/null 2>&1 || { 
    echo "####################################################################################"
    echo >&2 "Please install Hashicorp Vault command line interface"
    echo "####################################################################################"
    exit 1 
}

command -v aws >/dev/null 2>&1 || { 
    vault seal
    echo "####################################################################################"
    echo >&2 "Please install AWS command line interface"
    echo "####################################################################################"
    exit 1 
}

command -v cf >/dev/null 2>&1 || { 
    vault seal
    echo "####################################################################################"
    echo >&2 "Please install Cloud Foundry command line interface"
    echo "####################################################################################"
    exit 1 
}

echo -n "Enter the environment name (e.g. staging): "
read ENV

if [[ $1 != "environment-only" ]]; then
    echo -n "Enter the build version (e.g. v1.0.0): "
    read VER
    echo -n "Number of instances: "
    read INSTANCES
    
    if [ $ENV == "production" ]; then
       echo -n "You have chosen to deploy to production. Are you sure? [sure|no] : "
       read SURE
       if [ $SURE != "sure" ]; then
           exit 0
       fi
    fi
fi

####################################################################################
# Unseal the vault - will prompt for GitHub personal access token
# Vault is first sealed to ensure that deployment can't happen unless user has
# the unseal token. Mostly this is to avoid copy/paste unintended deployments. 
####################################################################################

vault seal
echo -n "Enter the vault unseal token: "
vault unseal

if [ $? == 1 ]; then
    echo "Unable to unseal vault"
    exit 1;
fi

####################################################################################
# Get AWS access keys to download the versioned package from S3
####################################################################################

AWS_ACCESS_KEY_ID=`vault read -field=AWS_ACCESS_KEY_ID secret/par/deploy/aws`
AWS_SECRET_ACCESS_KEY=`vault read -field=AWS_SECRET_ACCESS_KEY secret/par/deploy/aws`

####################################################################################
# Get environment variables that will be set on the target environment
####################################################################################

S3_ACCESS_KEY=`vault read -field=S3_ACCESS_KEY secret/par/env/$ENV`
S3_SECRET_KEY=`vault read -field=S3_SECRET_KEY secret/par/env/$ENV`
PAR_HASH_SALT=`vault read -field=PAR_HASH_SALT secret/par/env/$ENV`
S3_BUCKET_PUBLIC=`vault read -field=S3_BUCKET_PUBLIC secret/par/env/$ENV`
S3_BUCKET_PRIVATE=`vault read -field=S3_BUCKET_PRIVATE secret/par/env/$ENV`
S3_BUCKET_ARTIFACTS=`vault read -field=S3_BUCKET_ARTIFACTS secret/par/env/$ENV`
PAR_GOVUK_NOTIFY_KEY=`vault read -field=PAR_GOVUK_NOTIFY_KEY secret/par/env/$ENV`
PAR_GOVUK_NOTIFY_TEMPLATE=`vault read -field=PAR_GOVUK_NOTIFY_TEMPLATE secret/par/env/$ENV`
CLAMAV_HTTP_PASS=`vault read -field=CLAMAV_HTTP_PASS secret/par/env/$ENV`
CLAMAV_HTTP_USER=`vault read -field=CLAMAV_HTTP_USER secret/par/env/$ENV`
SENTRY_DSN=`vault read -field=SENTRY_DSN secret/par/env/$ENV`
SENTRY_DSN_PUBLIC=`vault read -field=SENTRY_DSN_PUBLIC secret/par/env/$ENV`

####################################################################################
# Reseal the vault
####################################################################################

echo "Resealing the vault.."
vault seal

if [[ $1 != "environment-only" ]]; then

    ####################################################################################
    # Pull the packaged version from S3
    ####################################################################################
    
    echo "Pulling version $VER"
    
    ####################################################################################
    # We are in the /cf directory
    # Pull the package to the build directory
    ####################################################################################
    
    BUILD_DIR=build-$ENV
    rm -rf $BUILD_DIR
    mkdir $BUILD_DIR
    
    cd $BUILD_DIR
    
    aws s3 cp s3://transform-par-beta-artifacts/builds/$VER.tar.gz .
    
    ####################################################################################
    # Check that we got the package
    ####################################################################################
    
    if [ ! -f $VER.tar.gz ]; then
       exit
    fi
    
    ####################################################################################
    # Unpack and remove package file
    ####################################################################################
    
    tar -zxvf $VER.tar.gz
    rm $VER.tar.gz
    
    ####################################################################################
    # Stay in the build directory to push the unpacked code
    ####################################################################################
    
    if [ ! -f cf/manifests/manifest.$ENV.yml ]; then
        echo "Manifest file cf/manifests/manifest.$ENV.yml not found"
        exit 1
    fi
    
    cf push -f cf/manifests/manifest.$ENV.yml --hostname par-beta-$ENV-green par-beta-$ENV-green 
fi

####################################################################################
# Set environment variables
####################################################################################

if [[ $1 == "environment-only" ]]; then
    TARGET_ENV=par-beta-$ENV
else
    TARGET_ENV=par-beta-$ENV-green
fi

cf set-env $TARGET_ENV S3_ACCESS_KEY $S3_ACCESS_KEY
cf set-env $TARGET_ENV S3_SECRET_KEY $S3_SECRET_KEY
cf set-env $TARGET_ENV PAR_HASH_SALT $PAR_HASH_SALT
cf set-env $TARGET_ENV S3_BUCKET_PUBLIC $S3_BUCKET_PUBLIC
cf set-env $TARGET_ENV S3_BUCKET_PRIVATE $S3_BUCKET_PRIVATE
cf set-env $TARGET_ENV S3_BUCKET_ARTIFACTS $S3_BUCKET_ARTIFACTS
cf set-env $TARGET_ENV APP_ENV $ENV
cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_KEY $PAR_GOVUK_NOTIFY_KEY
cf set-env $TARGET_ENV PAR_GOVUK_NOTIFY_TEMPLATE $PAR_GOVUK_NOTIFY_TEMPLATE
cf set-env $TARGET_ENV CLAMAV_HTTP_PASS $CLAMAV_HTTP_PASS
cf set-env $TARGET_ENV CLAMAV_HTTP_USER $CLAMAV_HTTP_USER
cf set-env $TARGET_ENV SENTRY_DSN $SENTRY_DSN
cf set-env $TARGET_ENV SENTRY_DSN_PUBLIC $SENTRY_DSN_PUBLIC

cf restage $TARGET_ENV

if [[ $1 != "environment-only" ]]; then

    cf ssh par-beta-$ENV-green -c "cd app/tools && python post_deploy.py"
    
    ####################################################################################
    # Blue/Green magic - switch domain routes to newly-deployed app
    ####################################################################################
    
    if [[ $ENV == "production" ]]; then
        CDN_DOMAIN="primary-authority.beis.gov.uk"
    else
        CDN_DOMAIN=$ENV-cdn.par-beta.co.uk
    fi
    
    cf map-route par-beta-$ENV-green cloudapps.digital -n par-beta-$ENV
    cf unmap-route par-beta-$ENV cloudapps.digital -n par-beta-$ENV
    cf map-route par-beta-$ENV-green $CDN_DOMAIN
    cf unmap-route par-beta-$ENV $CDN_DOMAIN
    cf delete par-beta-$ENV -f
    cf rename par-beta-$ENV-green par-beta-$ENV
    
    cf scale par-beta-$ENV -i $INSTANCES
fi
