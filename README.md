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
    vagrant ssh
    cd /vagrant
    
You are now inside your VM, in a directory containing a map of the repository code. Your host's .ssh directory (if you are on Ubuntu or Mac) is mapped to /vagrant/.ssh. Please modify the Vagrantfile accordingly to map to the correct SSH
directory if you are using a different operating system.

#### Setup the Docker development environment within the VM

    ssh-agent bash
    ssh-add /vagrant/.ssh/id_rsa

    cd /vagrant/docker
    sudo sh setup.sh
    
#### Bringing the Docker daemon up without installing dependencies

If your dependencies are already installed, you can bring the Docker daemon back up with:

    vagrant ssh (if required)
    cd /vagrant/docker
    sudo docker-compose up -d
    
#### Running a Drupal Refresh

    vagrant ssh (if required)
    cd /vagrant/docker
    docker exec -it par_beta_web bash -c "sh drupal-update.sh /var/www/html"
    
### Web Application

Please see the [web application readme file](https://github.com/TransformCore/beis-par-beta/blob/master/web/README.md) in the web directory for more information about Drupal and how to configure the web application.

### Dashboard

Please see the [dashboard readme file](https://github.com/TransformCore/beis-par-beta/blob/master/dashboard/README.md) in the dashboard directory for more information.

