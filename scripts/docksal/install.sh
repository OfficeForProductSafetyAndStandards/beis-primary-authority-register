fin restart
fin composer install &&
fin exec npm install &&
fin exec npm run frontend &&
fin drush --yes site:install &&
fin drush @par.paas --yes sql:drop &&
fin exec "drush @par.paas sql:cli < backups/db-dump-production-sanitised.sql" &&
fin drush pm:uninstall --yes dblog &&
fin drush pm:uninstall --yes maillog &&
fin drush --yes updatedb &&
fin drush --yes updatedb &&
fin drush --yes config:import &&
fin drush pm:uninstall --yes config_readonly &&
fin drush config:set --yes search_api.server.opensearch backend_config.connector_config.url http://beis-par-search:9200 &&
fin drush config:set --yes system.performance css.preprocess false &&
fin drush config:set --yes system.performance js.preprocess false &&
fin drush cache:rebuild &&
fin drush search-api:rebuild-tracker partnership_index &&
fin drush search-api:index partnership_index &&
fin drush user:login
