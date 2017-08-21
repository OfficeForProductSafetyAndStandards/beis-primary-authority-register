#!/bin/bash

####################################################################################
# Run this script from the /cf directory of the repository
# Usage:
#    ./push.sh <env> <version>
#
# e.g.
#    ./push.sh demo v0.0.33
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

ENV=$1
VER=$2

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
    echo "####################################################################################"
    echo >&2 "Please install AWS command line interface"
    echo "####################################################################################"
    vault seal
    exit 1 
}

command -v cf >/dev/null 2>&1 || { 
    echo "####################################################################################"
    echo >&2 "Please install Cloud Foundry command line interface"
    echo "####################################################################################"
    vault seal
    exit 1 
}

####################################################################################
# Unseal the vault - will prompt for GitHub personal access token
####################################################################################

vault unseal

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

####################################################################################
# Reseal the vault
####################################################################################

vault seal

####################################################################################
# Pull the packaged version from S3
####################################################################################

echo "Pulling version $VER"

####################################################################################
# We are in the /cf directory
# Pull the package to the build directory
####################################################################################

rm -rf build
mkdir build

cd build

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

pwd

if [ ! -f manifest.$ENV.yml ]; then
    echo "Manifest file manifest.$ENV.yml not found"
    exit 1
fi

echo manifest.$ENV.yml
cf push -f manifest.$ENV.yml

####################################################################################
# Set environment variables
####################################################################################

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

####################################################################################
# Go back to the /cf directory to set the domain, if any
####################################################################################

cd ..

if [ -f update-domain-$ENV.sh ]; then
    sh update-domain-$ENV.sh
fi
