# Dashboard

The dashboard we're using for development purposes uses Smashing, and integrates with Github, Travis, Production metrics amongst other useful real time stats.

## Pre-requisites include
* Ruby ^3.0
* Ruby gems smashing and bundler

## Configuration
To configure the production endpoint set the environment var PROD_ENDPOINT:

    export PROD_ENDPOINT="https://par-beta-test.cloudapps.digital/"
    
To configure the AWS keys for accessing the Test reports set the environment vars ARTIFACTS_KEY and ARTIFACTS_SECRET:

    export AWS_ACCESS_KEY="PLEASE_GENERATE_A_KEY"
    export SECRET_ACCESS_KEY="PLEASE_GENERATE_A_SECRET"

## Instalation
Then bring the dashboard up by running:

    cd dashboard
    bundle
    smashing start
    
## Accessing

Smashing starts the thin webserver so that the smashing dashboard will then be accessible at http://127.0.0.1:8112/devops

Check out http://smashing.github.io/ for more information.
