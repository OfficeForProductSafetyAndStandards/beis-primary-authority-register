# Department of Business Energy and Industrial Strategy
## Primary Authority Register

[![Build Status](https://travis-ci.org/TransformCore/beis-par-beta.svg?branch=master)](https://travis-ci.org/TransformCore/beis-par-beta)

Herein lie the fruits of our endeavours to create a world class digital service for Regulatory Authority.

### Vagrant development environment

The Vagrant development environment wraps a virtual machine around the Docker setup (below). This resolves some issues with speed when using Docker for Mac.

#### Prerequisites

    [VirtualBox](https://www.virtualbox.org/wiki/Downloads) - tested with version 5.1.22
    [Vagrant](https://www.vagrantup.com/downloads.html) - tested with version 1.9.6
    
#### Create the VM

    vagrant up
    
You should now have a running VM within which is a running Docker daemon. You can access the site at:

    http://http://192.168.82.68:8111/
    
#### Clearing the Drupal cache

    vagrant ssh
    cd /vagrant/docker
    sudo sh clear-drupal-cache.sh
    
#### Reloading test data

    vagrant ssh
    docker exec -i par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < docker/fresh_drupal_postgres.sql"
    
#### Reinstalling dependencies

    vagrant ssh
    docker exec -i par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'
    docker exec -i par_beta_web bash -c "cd /var/www/html/tests && rm -rf node_modules/* && ../../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "rm -rf node_modules/* && ../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm run gulp"    
    
### Web Application

Please see the [web application readme file](https://github.com/TransformCore/beis-par-beta/blob/master/web/README.md) in the web directory for more information about Drupal and how to configure the web application.

### Dashboard

Please see the [dashboard readme file](https://github.com/TransformCore/beis-par-beta/blob/master/dashboard/README.md) in the dashboard directory for more information.

