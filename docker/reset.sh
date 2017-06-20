docker rm --force `docker ps -qa` && docker rmi $(docker images -q) && docker-compose up -d --force-recreate --build && sh setup.sh
