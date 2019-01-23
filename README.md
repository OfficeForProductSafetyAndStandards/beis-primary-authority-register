# Department of Business Energy and Industrial Strategy - Regulatory Authority

# Primary Authority Register

[![Build Status](https://travis-ci.org/UKGovernmentBEIS/beis-primary-authority-register.svg?branch=master)](https://travis-ci.org/UKGovernmentBEIS/beis-primary-authority-register)

## Web Application

Please see the [web application readme file](https://github.com/TransformCore/beis-par-beta/blob/master/web/README.md) in the web directory for more information about Drupal and how to configure the web application.

## Dashboard

Please see the [dashboard readme file](https://github.com/TransformCore/beis-par-beta/blob/master/dashboard/README.md) in the dashboard directory for more information.

## Vagrant development environment

The Vagrant development environment tries to maximise all the work done for Drupal-VM as much as possible. Please follow the Drupal-VM readme below and install Drupal-VM in a convenient location.

*NOTE:* The old Vagrant development environment wraps docker in a VM, these files vagrant files are still in the repository but please do not use them.

#### Prerequisites

* [VirtualBox](https://www.virtualbox.org/wiki/Downloads) - tested with version 5.1.22
* [Vagrant](https://www.vagrantup.com/downloads.html) - tested with version 1.9.6
* [Drupal-VM](https://github.com/kalpaitch/drupal-vm)
* A copy of the [Drupal-VM config.yml file](https://s3.eu-west-2.amazonaws.com/beis-par-artifacts/dev/config.yml) in the BEIS S3 artifacts bucket.
* A copy of the [latest sanitised PAR database](https://s3.eu-west-2.amazonaws.com/beis-par-artifacts/backups/drush-dump-production-sanitized-latest.sql.tar.gz) from the BEIS S3 artifacts bucket.

#### Configuration

Before starting the Drupal-VM make sure that you have cloned a copy of the website and run all the necessary setup on this. You will need to configure Drupal-VM so that the `vagrant_synced_folders` for this project points to the correct `local_path` of your application.

As part of the site setup run composer install from the project root folder.  Ensure the vendor directory is created with all the required application components before moving on to the database section.

#### Database

In order to run the site you will need to import a copy of the latest par database, download this and place in the `backups` directory of the par project (create the folder if it doesn't' exist).

Import the database using drush (note the database should be truncated before re-importing)
```bash
cd /var/www/par/web
tar -zxvf drush-dump-production-sanitized-latest.sql.tar.gz drush-dump-production-sanitized-latest.sql
../vendor/bin/drush sql-cli < ../backups/drush-dump-production-sanitized-latest.sql
```
    
## Deployment

Tagging the master branch will cause the build to be packaged and stored in S3

    git tag v0.0.31
    git push --tags
    
Once a build has been packaged, it can be deployed to another environment as follows:

    cd cf
    ./push.sh ENV_NAME VERSION
    
e.g.

    ./push.sh staging v0.0.31    
    
Full instructions on setting AWS keys and environment variables for the target environment can be found in the push.sh script itself.

#### Prerequisites

* [Vault](https://www.vaultproject.io/)
* [AWS CLI](https://aws.amazon.com/cli/)

## Backup database

The build relies on a seed database which is a sanitised version of the production database. At regular periods this seed database needs to be updated:

#### Backup the production database
```
cf ssh par-beta-production -c "python app/devops/tools/postgres_dump.py"
```

#### Sanitize the production database
Go to the S3 artifacts bucket and download a copy of the drush-dump-production-unsanitized.sql file that was just created (and uploaded).

**NOTE:** You must always sanitize the database against the latest release code _only_ with dev and test settings files turned off:
```
$config['config_split.config_split.dev_config']['status'] = FALSE;
$config['config_split.config_split.test_config']['status'] = FALSE;
```

Import the newly downloaded production db:
```
cd ./web
tar -zxvf ./location/of/downloaded/sql/drush-dump-production-unsanitized-latest.sql.tar.gz
../vendor/bin/drush @paas sql-drop -y
../vendor/bin/drush @paas sql-cli < ./location/of/downloaded/sql/drush-dump-production-unsanitized-latest.sql
cd ..
sh drupal-update.sh
```

Check that the data looks right and then sanitise:
```
cd ./web
../vendor/bin/drush @paas sql-sanitize -y
```

Then dump the db, zip it and upload it back to the S3 artifacts bucket with the correct name:
```../vendor/bin/drush @paas sql-dump --result-file=./drush-dump-production-sanitized-latest.sql --extra="-O -x"
tar -zcvf drush-dump-production-sanitized-latest.sql.tar.gz -C ./ drush-dump-production-sanitized-latest.sql
../vendor/bin/drush fsp s3backups drush-dump-production-sanitized-latest.sql.tar.gz drush-dump-production-sanitized-latest.sql.tar.gz```
