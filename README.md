# Department of Business Energy and Industrial Strategy

## Primary Authority Register

[![Build Status](https://travis-ci.org/TransformCore/beis-par-beta.svg?branch=master)](https://travis-ci.org/TransformCore/beis-par-beta)

Herein lie the fruits of our endeavours to create a world class digital service for Regulatory Authority.

### Web Application

Please see the [web application readme file](https://github.com/TransformCore/beis-par-beta/blob/master/web/README.md) in the web directory for more information about Drupal and how to configure the web application.

### Dashboard

Please see the [dashboard readme file](https://github.com/TransformCore/beis-par-beta/blob/master/dashboard/README.md) in the dashboard directory for more information.

### Vagrant development environment

The Vagrant development environment wraps a virtual machine around the Docker setup (below). This resolves some issues with speed when using Docker for Mac.

#### Prerequisites

* [VirtualBox](https://www.virtualbox.org/wiki/Downloads) - tested with version 5.1.22
* [Vagrant](https://www.vagrantup.com/downloads.html) - tested with version 1.9.6
    
#### Create the VM

    vagrant up
    
You should now have a running VM within which is a running Docker daemon. You can access the site at:

    http://192.168.82.68:8111/
    
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
