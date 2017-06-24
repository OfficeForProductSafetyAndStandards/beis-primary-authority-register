rm ../web/sites/default/settings.local.php
docker-compose up -d --force-recreate --build && sh setup.sh
