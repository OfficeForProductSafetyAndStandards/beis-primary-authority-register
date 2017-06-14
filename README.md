# beis-par-beta

Herein lies the fruits of our endevour to create a world class digital service for Regulatory Authority.

## Docker development environment

Install docker (or Docker for Windows, or Docker for Mac), then:

    cd docker
    
NOTE: Linux users will need to "sudo" all the following commands

    docker-compose up -d
	
Load the test data and setup the environment:

    sh setup.sh
    
Request the hash salt from another member of the team and add this to the hash setting at the bottom of your local settings file:

    vi ../sites/default/settings.local.php
    
You can then visit the site at:

    http://127.0.0.1:8111





