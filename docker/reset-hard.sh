# This command will fail unless you have running images and running containers
docker rm --force `docker ps -qa` && docker rmi $(docker images -q) --force && docker-compose up -d --force-recreate --build && sh setup.sh
