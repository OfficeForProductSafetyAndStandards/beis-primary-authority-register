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
    
The Docker daemon should now be running. If you have all your dependecies (Composer, NPM, Gulp) already installed and have imported the database and run the Drupal update commands, you should be good-to-go.

Otherwise, you can run the setup.sh file to run through these processes, or you can cherry pick from the setup.sh file to perform the stages that you need.

    vagrant ssh
    cd /vagrant/docker
    sudo sh setup.sh
    
#### Clearing the Drupal cache

    vagrant ssh
    cd /vagrant/docker
    sudo sh clear-drupal-cache.sh
    
### Web Application

Please see the [web application readme file](https://github.com/TransformCore/beis-par-beta/blob/master/web/README.md) in the web directory for more information about Drupal and how to configure the web application.

### Dashboard

Please see the [dashboard readme file](https://github.com/TransformCore/beis-par-beta/blob/master/dashboard/README.md) in the dashboard directory for more information.

