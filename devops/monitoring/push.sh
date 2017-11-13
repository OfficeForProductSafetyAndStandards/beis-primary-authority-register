#!/bin/bash

####################################################################################
# Unseal the vault - will prompt for GitHub personal access token
# Vault is first sealed to ensure that deployment can't happen unless user has
# the unseal token. Mostly this is to avoid copy/paste unintended deployments. 
####################################################################################

export VAULT_ADDR=https://vault.primary-authority.beis.gov.uk:8200

vault seal
vault unseal $GOVUK_VAULT_UNSEAL_KEY

if [ $? != 0 ]; then
    exit 1;
fi

vault auth $GOVUK_VAULT_AUTH_TOKEN

if [ $? != 0 ]; then
    exit 1;
fi

####################################################################################
# Get environment variables that will be set on the target environment
####################################################################################

BEIS_PAR_PUBNUB_SUBSCRIBE_KEY=`vault read -field=BEIS_PAR_PUBNUB_SUBSCRIBE_KEY secret/par/monitor/production`

if [ $? != 0 ]; then
	exit 1
    echo "################################################################################################"
    echo "Error reading from vault."
    echo "################################################################################################"
fi

BEIS_PAR_PUBNUB_PUBLISH_KEY=`vault read -field=BEIS_PAR_PUBNUB_PUBLISH_KEY secret/par/monitor/production`
BEIS_PAR_CF_APP_KEY=`vault read -field=BEIS_PAR_CF_APP_KEY secret/par/monitor/production`
BEIS_PAR_CF_API_AUTH_TOKEN=`cf oauth-token`

####################################################################################
# Reseal the vault
####################################################################################

echo "Resealing the vault.."
vault seal
    
cf push

####################################################################################
# Set environment variables
####################################################################################

TARGET_ENV=par-beta-monitoring

cf set-env $TARGET_ENV BEIS_PAR_PUBNUB_SUBSCRIBE_KEY $BEIS_PAR_PUBNUB_SUBSCRIBE_KEY
cf set-env $TARGET_ENV BEIS_PAR_PUBNUB_PUBLISH_KEY $BEIS_PAR_PUBNUB_PUBLISH_KEY
cf set-env $TARGET_ENV BEIS_PAR_CF_APP_KEY $BEIS_PAR_CF_APP_KEY
cf set-env $TARGET_ENV BEIS_PAR_CF_API_AUTH_TOKEN "$BEIS_PAR_CF_API_AUTH_TOKEN"

cf restage $TARGET_ENV
