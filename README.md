# Department of Business Energy and Industrial Strategy - Regulatory Authority

## Primary Authority Register

[![Build Status](https://travis-ci.org/TransformCore/beis-par-beta.svg?branch=master)](https://travis-ci.org/TransformCore/beis-par-beta)

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

If you already have the repository cloned, destroy it and reclone it. Also shutdown and delete any existing VirtualBox VMs for beis-par-beta.

    rm -rf beis-par-beta
    git clone git@github.com:TransformCore/beis-par-beta
    cd beis-par-beta
    
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
    cd /vagrant/devops/scripts
    sh drupal-update.sh
    
##### Reloading test data

    vagrant ssh
    docker exec -i par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < devops/docker/fresh_drupal_postgres.sql"
    
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

