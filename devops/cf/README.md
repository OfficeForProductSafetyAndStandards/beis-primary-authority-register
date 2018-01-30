# Setting up a new environment

## Prerequisites

* Cloud Foundry tools and login credentials for Gov.uk PaaS
* Hashicorp Vault tools and login credentials for the beis-primary-authority-register project vault
* AWS tools and keys for the beis-primary-authority-register project buckets

## Setup

### Choose an environment name

Each environment has a slug which will be used consistently throughout the config and deployment of the environment to define such things as

* Domain names
* Config file names
* Environment variables
* Vault data locations
* App and service names

In this file, this slug will be written as ENV_SLUG

### Create the manifest file in /cf/manifests

The manifest file describes the config of the Cloud Foundry application, including memory and disk space quotas.

It also details which Cloud Foundry services are to be bound to the app. In our case, the one service is a Postgres database instance.

Copy an existing manifest and modify to taste. Use the app slug in the manifest file name and where required within the manifest contents.

    cf/manifests/manifest.ENV_SLUG.yml

### Create the Postgres service

#### Choose a service plan

    cf marketplace -s postgres
    
#### Create the service

    cf create-service postgres M-HA-dedicated-9.5 par-pg-assessment
    
#### Create the vault entries

    The script cf/update-vault.sh shows how to read and set values in the vault. 
    
    The script simply sets the same values as it reads, but variations on the script can be used to either copy variables from another environment or create new ones.
    
    Remember that any keys not specified in the update command will be deleted, so all key/value pairs must be provided with each call. 
    
#### Push the app

Use the push script to deploy the application to PaaS.

We skip the post deploy scripts, as we need to seed the database before those scripts can run.

From the root of the beis-primary-authority-register repository:
   
    cf push -f cf/manifests/manifest.ENV_SLUG.yml
    
This will create the app with the code as it stands on your local machine. This is just a placeholder until we perform a correct deployment which will specify a version
of the packaged code to push.
    
#### Setup the CDN and CNAME record

Run the cf/route.sh script

    cd cf
    ./route.sh
    
The script will prompt for the environment slug.

Once complete, type

    cf service par-cdn-ENV_SLUG
    
This will give you the information you need to set up the CNAME for the par-beta.co.uk domain

#### Set the environment variables

    cd cf
    ./push.sh environment-only
    
#### Import the seed data

    cf ssh par-beta-ENV_SLUG
    cd app/tools
    python import_fresh_db.py -f /path/to/sql/file.sql
    
