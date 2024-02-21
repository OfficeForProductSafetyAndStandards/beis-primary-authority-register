# Secret management

Secrets are managed through a self-hosted [Vault](https://developer.hashicorp.com/vault/docs/what-is-vault) system.

This is managed and hosted on AWS and accessible at https://vault.primary-authority.services:8200.

> See AWS managed services

Vault is currently

> See Vault readme at `/devops/vault`.

## Get the current secret values

Note: To access secrets in vault a token, and an unsealing key are required.

Secrets are stored in the vault secret engine at `secret/par/env/[SECRET]` where `[SECRET]` can be one of:
* `ci` - used by all CI builds
* `paas` - used by all non-production instances
* `production` - used in production
* `staging` - used in staging

They can be accessed by unsealing the vault and running `vault kv get -tls-skip-verify -mount=secret /par/env/[SECRET]`. Optionally, the `-field=[KEY]` flag can be added to access a specifiv key/value pair within the secret.

Within each secret are a set of key/values that represent the names of environment variables within PAR along with their values.

An example of the current environment variables can be found in the root `.env.example` file.

## Updating secrets

Note: To update secrets in vault a token, and an unsealing key are required.

```
./update-vault.sh [SECRET] [KEY] 'value'
```