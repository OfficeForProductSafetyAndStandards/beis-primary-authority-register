fin restart
fin composer install &&
fin exec npm install &&
fin exec npm run frontend &&
# fin drush --yes site:install &&
fin drush @par.paas --yes sql:drop &&
fin drush @par.paas sql:cli < backups/db-dump-production-sanitised.sql &&
fin drush --yes updatedb &&
fin drush --yes config:import &&
fin drush pm:uninstall --yes config_readonly &&
fin drush config:set --yes search_api.server.opensearch backend_config.connector_config.url https://os:9200 &&
fin drush config:set --yes system.performance css.preprocess false &&
fin drush config:set --yes system.performance js.preprocess false &&
fin drush cache:rebuid &&
fin drush user:login
