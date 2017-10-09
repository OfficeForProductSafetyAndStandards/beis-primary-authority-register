sh destroy-dependencies.sh
docker exec -i par_beta_web bash -c 'su - composer -c "cd /var/www/html && php composer.phar install"'
docker exec -i par_beta_web bash -c "cd /var/www/html/tests && rm -rf node_modules/* && /usr/local/n/versions/node/7.2.1/bin/npm install"
docker exec -i par_beta_web bash -c "rm -rf node_modules/* && /usr/local/n/versions/node/7.2.1/bin/npm install"
docker exec -i par_beta_web bash -c "/usr/local/n/versions/node/7.2.1/bin/npm run gulp" 
if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
    cat ../web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
fi
