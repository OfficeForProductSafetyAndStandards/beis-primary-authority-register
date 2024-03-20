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
  'database' => getenv('MYSQL_DATABASE'),
  'username' => getenv('MYSQL_USER'),
  'password' => getenv('MYSQL_PASSWORD'),
  'host' => getenv('MYSQL_HOST'),
  'driver' => 'mysql',
  'prefix' => 'fiss2_',
  'collation' => 'utf8mb4_general_ci',
  'charset' => 'utf8mb4',
];
```

## ClamAV
A container provides the service which is used by this web site.

From clamav.settings.yml in the config/default/docksal folder.

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

# Notes

- Drupal is a [registered trademark](https://drupal.com/trademark) of [Dries
  Buytaert](https://dri.es/).
