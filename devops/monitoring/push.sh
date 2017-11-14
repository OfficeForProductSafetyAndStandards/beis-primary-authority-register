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

CF_ENDPOINT=`vault read -field=CF_ENDPOINT secret/par/monitor/cf`

if [ $? != 0 ]; then
	exit 1
    echo "################################################################################################"
    echo "Error reading from vault."
    echo "################################################################################################"
fi

CF_LOGIN_EMAIL=`vault read -field=CF_LOGIN_EMAIL secret/par/monitor/cf`
CF_LOGIN_ENDPOINT=`vault read -field=CF_LOGIN_ENDPOINT secret/par/monitor/cf`
CF_LOGIN_PASSWORD=`vault read -field=CF_LOGIN_PASSWORD secret/par/monitor/cf`
PUBNUB_PUBLISH_KEY=`vault read -field=PUBNUB_PUBLISH_KEY secret/par/monitor/cf`
PUBNUB_SUBSCRIBE_KEY=`vault read -field=PUBNUB_SUBSCRIBE_KEY secret/par/monitor/cf`

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

cf set-env $TARGET_ENV CF_ENDPOINT $CF_ENDPOINT
cf set-env $TARGET_ENV CF_LOGIN_EMAIL $CF_LOGIN_EMAIL
cf set-env $TARGET_ENV CF_LOGIN_ENDPOINT $CF_LOGIN_ENDPOINT
cf set-env $TARGET_ENV CF_LOGIN_PASSWORD $CF_LOGIN_PASSWORD
cf set-env $TARGET_ENV PUBNUB_PUBLISH_KEY $PUBNUB_PUBLISH_KEY
cf set-env $TARGET_ENV PUBNUB_SUBSCRIBE_KEY $PUBNUB_SUBSCRIBE_KEY

cf restage $TARGET_ENV
