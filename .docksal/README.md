# Notes

## Spaces in root directory path
It has been noted that with the current version of *fin*, it does not cope well
with having spaces in the root directory path.

## DB
Please note that in settings.php in the server directory, the database 'host'
parameter needs to be *db* and **not** *localhost* to work in this
environment. Better still use the environmental variables, for example
```php
$databases['default']['default'] = [
  'database' => getenv('POSTGRES_DB'),
  'username' => getenv('POSTGRES_USER'),
  'password' => getenv('POSTGRES_PASSWORD'),
  'host' => getenv('POSTGRES_HOST'),
  'driver' => 'pgsql',
  'prefix' => '',
];
```

## ClamAV
A container provides the service which is used by this web site.

From clamav.settings.yml in the sync-docksal folder.

```yaml
scan_mode: '0'
mode_daemon_tcpip:
  hostname: clamav
  port: '3310'
```

## PHPUnit
These are set in .docksal/docksal.yml, to inject them into the docker container
using information from the environment configuration files used by ```fin```.
See [Custom Configuration](https://docs.docksal.io/stack/custom-configuration/)
for more information about these files.

## Cron jobs
Developers need to be aware of this Docksal issue [Make Docker-set environment variables accessible in cron jobs](https://github.com/docksal/service-cli/issues/188),

## Local docksal configuration files
Both docksal-local.yml and docksal-local.env are in the [.gitignore](.gitignore)
file and it recommended the use of symbolic links to include them in this
directory from outside the git repository file structure.

# Installation script
There is an [installation script](../scripts/docksal/install.sh) in $PROJECT_ROOT/scripts/docksal/install.sh .
It is expected that it is run from outside the cli container.
It expects to import a SQL file call db-dump-production-sanitised.sql from the
directory [backups](../backups).

If you get this message
```bash
In DefinitionErrorExceptionPass.php line 51:

  You have requested a non-existent parameter "cache_lifecycle_bins".
```

then it is suggested that you add the following to the docksal-local.yml file
```yaml
parameters:
  cache_lifecycle_bins:
    - 'config'
```
to [services.local.non-production.yml](../web/sites/default/services.local.non-production.yml)
it probably can be removed at a later date and does not have to be committed.

# Opensearch

If opensearch does not work, then use the following command to see the logs from the container

```bash
fin logs os
```

If you see the following message

```bash
max virtual memory areas vm.max_map_count [65530] is too low, increase to at least [262144]
```

then run the following command

```bash
sudo sysctl -w vm.max_map_count=262144
```

# Notes

- Drupal is a [registered trademark](https://drupal.com/trademark) of [Dries
  Buytaert](https://dri.es/).
