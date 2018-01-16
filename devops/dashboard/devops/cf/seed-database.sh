CF_ROOT_DOMAIN="cfapps.io"
CF_ENDPOINT=api.run.pivotal.io
CF_ORG=netsensia
CF_SPACE=golfingrecord
APP_NAME=golfingrecord-auth

export VAULT_ADDR=https://vault.netsensia.com:8200

echo -n "Enter your Cloud Foundry username: "
[ -z "$CF_USER" ] && read CF_USER
echo -n "Enter your Cloud Foundry password (will be hidden): "
[ -z "$CF_PASS" ] && read -s CF_PASS
cf login -a $CF_ENDPOINT -u $CF_USER -p $CF_PASS
if [ $? != 0 ]; then
    exit
fi

echo -n "Enter the environment name (e.g. staging): "
read ENV

if [[ $ENV == "production" ]]; then
   echo -n "You have chosen to deploy to production. Are you sure? [sure|no] : "
   read SURE
   if [ $SURE != "sure" ]; then
       exit 0
   fi
fi

vault seal
echo -n "Enter the vault unseal token: "

if [ -z "$VAULT_UNSEAL_KEY" ]; then
    vault unseal
else
    vault unseal $VAULT_UNSEAL_KEY
fi

echo -n "Enter your vault authentication token:"

if [ -z "$VAULT_AUTH_TOKEN" ]; then
    vault auth
else
    vault auth $VAULT_AUTH_TOKEN
fi

if [ $? != 0 ]; then
    exit 1;
fi

cf target -o $CF_ORG -s $CF_SPACE-$ENV

cf create-service cleardb spark mysql-$APP_NAME
cf bind-service $APP_NAME mysql-$APP_NAME
cf restage $APP_NAME
cf ssh $APP_NAME -c "cd app/tools && python php.py \"artisan lumen-oauth2-server:seed\""

