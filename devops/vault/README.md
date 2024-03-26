# Vault

Vault is used to store our application credentials.

It is self-hosted on an ec2 instance within the AWS account. All the values are also backed up in AWS Secrets Manager in case the vault service is accidentally terminated.

The URL for this service is https://vault.primary-authority.services:8200

## Access

Access to this instance is controlled with a root token and unseal key, these can be found in the [development .env](s3://beis-par-artifacts/dev/.env) file.

## Vault stores

Vault contains a number of different stores which contain all the secrets for each environment, loosely there is one store for each different environment type, which can be listed as follows:

```
vault kv list -tls-skip-verify secret/par/env/
```

- `ci` - contains all the secrets for use in CI.
- `paas` - contains all the secrets for non-production paas instances.
- `production` - contains all secrets for the production environment.
- `staging` - contains all the secrets for the staging environment.

**Note:** Development values are not stored within vault, instead they are usually set in the [development .env](s3://beis-par-artifacts/dev/.env) file or overridden in the [settings.local.php](s3://beis-par-artifacts/dev/settings.local.php) file.

## Retrieving values

To retrieve a value from a secret store, where `$VAR_NAME` is the environment variable to retrieve, and `$SECRET_STORE` is one of the stores mentioned above, run:

```
vault kv get --field=$VAR_NAME -tls-skip-verify secret/par/env/$SECRET_STORE
```

It is also possible to retrieve all the values for a store by leaving out the `--field` options.

## Updating values

There is a helper script to make updating single values within a store easier to manage.

To update a value in a secret store, where `$VAR_NAME` is the environment variable to set, and `$SECRET_STORE` is one of the stores mentioned above, run:

```
./update-vault.sh $SECRET_STORE $VAR_NAME $VALUE
```
