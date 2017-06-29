sh destroy-containers.sh
rm ../web/sites/default/settings.local.php
rm -rf ../vendor/*
rm -rf ../node_modules/*
rm -rf ../tests/node_modules/*
docker-compose up -d --force-recreate 
sh setup.sh
