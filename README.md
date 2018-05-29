# Department of Business Energy and Industrial Strategy - Regulatory Authority

## Primary Authority Register

[![Build Status](https://travis-ci.org/UKGovernmentBEIS/beis-primary-authority-register.svg?branch=master)](https://travis-ci.org/UKGovernmentBEIS/beis-primary-authority-register)

### Web Application

Please see the [web application readme file](https://github.com/TransformCore/beis-par-beta/blob/master/web/README.md) in the web directory for more information about Drupal and how to configure the web application.

### Dashboard

Please see the [dashboard readme file](https://github.com/TransformCore/beis-par-beta/blob/master/dashboard/README.md) in the dashboard directory for more information.

### Vagrant development environment

The Vagrant development environment wraps a virtual machine around the Docker setup (below). This resolves some issues with speed when using Docker for Mac.

#### Prerequisites

* [VirtualBox](https://www.virtualbox.org/wiki/Downloads) - tested with version 5.1.22
* [Vagrant](https://www.vagrantup.com/downloads.html) - tested with version 1.9.6

#### Destroy everything

If you already have the repository cloned, destroy it and reclone it. Also shutdown and delete any existing VirtualBox VMs for beis-primary-authority-register.

    rm -rf beis-primary-authority-register
    git clone git@github.com:UKGovernmentBEIS/beis-primary-authority-register
    cd beis-primary-authority-register
    
#### Setup environment variables

In the root of the project, copy .env.example to .env . Please get the values from another team member.

#### Create the VM

    vagrant up
    
You should now have a running VM within which is a running Docker daemon. You can access the site at:

    http://192.168.82.68:8111/
    
### Re-installing dependencies, clearing caches, etc...

If you want to rebuild without re-cloning the repository:

    vagrant ssh
    cd /vagrant/docker
    sh setup.sh
    
#### Some useful commands
    
##### Clearing the Drupal cache

    vagrant ssh
    cd /vagrant/docker
    sudo sh clear-drupal-cache.sh
    
##### Running a Drupal update (includes clearing the cache)

    vagrant ssh
    cd /vagrant
    sh drupal-update.sh
    
##### Reloading test data

    vagrant ssh
    docker exec -i par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < docker/fresh_drupal_postgres.sql"
    
##### Refreshing dependencies

    vagrant ssh
    cd /vagrant/docker
    sudo sh refresh-dependencies.sh
    
### Deployment

Every successfull build of the master branch gets deployed to

    https://par-beta-continuous.cloudapps.digital
    
The currently deployed build can be found using

    https://par-beta-continuous.cloudapps.digital/build_version.txt
    
### Deploying to other environments

Tagging the master branch will cause the build to be packaged and stored in S3

    git tag v0.0.31
    git push --tags
    
Once a build has been packaged, it can be deployed to another environment as follows:

    cd cf
    ./push.sh ENV_NAME VERSION
    
e.g.

    ./push.sh staging v0.0.31    
    
Full instructions on setting AWS keys and environment variables for the target environment can be found in the push.sh script itself.


### Backup database

The build relies on a seed database which is a sanitised version of the production database. At regular periods this seed database needs to be updated:

#### Backup the production database
```
cf ssh par-beta-production -c "python app/devops/tools/postgres_dump.py"
```

#### Sanitize the production database
Go to the S3 artifacts bucket and download a copy of the drush-dump-production-unsanitized.sql file that was just created (and uploaded).

**NOTE:** You must always be in master branch with dev and test settings files turned off:
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
