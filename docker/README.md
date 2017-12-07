## Docker Development Environment

### Mac users

To speed up file sync between the web container and your host, you will need to use docker-sync. Docker for Mac is considerably (~50x) slower maintaining the link between host and container files.

After following the setup steps above, run the following:

    sudo gem install docker-sync
    docker-sync-start start

### Installation

    sudo gem install docker-sync

### Sync stop/stop/restart

    docker-sync start
    docker-sync stop
    docker-sync clean
    docker-sync list

### View sync log

    cd docker
    tail -f .docker-sync/daemon.log

## Destroy containers and images and rebuild

    docker rm --force `docker ps -qa` && docker rmi $(docker images -q) && docker-compose up -d --force-recreate --build && sh setup.sh
