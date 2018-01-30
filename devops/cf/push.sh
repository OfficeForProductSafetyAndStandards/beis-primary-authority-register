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

CURRENT_DIR=${PWD##*/}

if [ $CURRENT_DIR != "cf" ]; then
    echo "################################################################################################"
    echo >&2 "Please run this script from the /devops/cf directory of the repository"
    echo "################################################################################################"
    exit 1
fi

command -v vault >/dev/null 2>&1 || { 
    echo "################################################################################################"
    echo >&2 "Please install Vault CLI - https://www.vaultproject.io/docs/install/index.html"
    echo "################################################################################################"
    exit 1 
}

command -v aws >/dev/null 2>&1 || { 
    echo "################################################################################################"
    echo >&2 "Please install AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html"
    echo "If you set it up in a Python virtual env, you may need to run workon"
    echo "################################################################################################"
    exit 1 
}

command -v cf >/dev/null 2>&1 || { 
    echo "################################################################################################"
    echo >&2 "Please install Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html"
    echo "################################################################################################"
    exit 1 
}

if [[ -z "${GOVUK_CF_USER}" ]]; then
    echo -n "Enter your Cloud Foundry username: "
    read CF_USER
    echo "Note: You can set a default username by setting the GOVUK_CF_USER environment variable"
else
    CF_USER=$GOVUK_CF_USER
    echo "Username: $CF_USER"
fi

echo -n "Enter your Cloud Foundry password (will be hidden): "
read -s CF_PASS
cf login -a api.cloud.service.gov.uk -u $CF_USER -p $CF_PASS
if [ $? != 0 ]; then
    exit
fi

echo -n "Enter the environment name (e.g. staging): "
read ENV

if [[ $1 != "environment-only" ]]; then
    echo -n "Enter the build version (e.g. v1.0.0): "
    read VER
    echo -n "Number of instances: "
    read INSTANCES
    
    if [[ $ENV == "production" ]]; then
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

export VAULT_ADDR=https://vault.primary-authority.beis.gov.uk:8200

vault seal
echo -n "Enter the vault unseal token: "
vault unseal

if [ $? != 0 ]; then
    exit 1;
fi

####################################################################################
# Get AWS access keys to download the versioned package from S3
####################################################################################

echo -n "Enter your vault authentication token:"

vault auth

if [ $? != 0 ]; then
    exit 1;
fi

export AWS_ACCESS_KEY_ID=`vault read -field=AWS_ACCESS_KEY_ID secret/par/deploy/aws`
export AWS_DEFAULT_REGION="eu-west-1"

if [ $? != 0 ]; then
	exit 1
    echo "################################################################################################"
    echo "Error reading from vault."
    echo "################################################################################################"
fi

export AWS_SECRET_ACCESS_KEY=`vault read -field=AWS_SECRET_ACCESS_KEY secret/par/deploy/aws`

####################################################################################
# Get environment variables that will be set on the target environment
####################################################################################

VAULT_ENV_VARS=( \
    "S3_ACCESS_KEY" \
    "S3_SECRET_KEY" \
    "PAR_HASH_SALT" \
    "S3_BUCKET_PUBLIC" \
    "S3_BUCKET_PRIVATE" \
    "S3_BUCKET_ARTIFACTS" \
    "PAR_GOVUK_NOTIFY_KEY" \
    "PAR_GOVUK_NOTIFY_TEMPLATE" \
    "CLAMAV_HTTP_PASS" \
    "CLAMAV_HTTP_USER" \
    "SENTRY_DSN" \
    "SENTRY_DSN_PUBLIC" \
)

I=0
for VAR_NAME in "${VAULT_ENV_VARS[@]}"
do
    ENV_VAR_VALUES[$I]=`vault read -field=$VAR_NAME secret/par/env/$ENV`
    I=$((I+1))
done

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
    sudo rm -rf $BUILD_DIR
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
    sudo chmod -R 755 web/sites/default
    rm $VER.tar.gz
    
    ####################################################################################
    # Stay in the build directory to push the unpacked code
    ####################################################################################
    
    MANIFEST=../manifests/manifest.$ENV.yml
    if [ ! -f $MANIFEST ]; then
        echo "Manifest file $MANIFEST not found"
        exit 1
    fi
    
    cf push -f $MANIFEST --hostname par-beta-$ENV-green par-beta-$ENV-green 
fi

####################################################################################
# Set environment variables
####################################################################################

if [[ $1 == "environment-only" ]]; then
    TARGET_ENV=par-beta-$ENV
else
    TARGET_ENV=par-beta-$ENV-green
fi

I=0
for ENV_VAR_NAME in "${VAULT_ENV_VARS[@]}"
do
    cf set-env $TARGET_ENV $ENV_VAR_NAME ${ENV_VAR_VALUES[$I]}
    I=$((I+1))
done

cf set-env $TARGET_ENV APP_ENV $ENV

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

    cf ssh par-beta-$ENV -c "cd app/tools && python cron_runner.py"
    cf ssh par-beta-$ENV -c "cd app/tools && python cache_warmer.py"
fi
