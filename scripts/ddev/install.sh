ddev restart
ddev composer install &&
npm install &&
npm run frontend &&
ddev drush si -y &&
ddev drush @par.paas sql:drop -y &&
ddev drush @par.paas sql:cli < backups/db-dump-production-sanitised.sql &&
ddev drush updb -y &&
ddev drush features:import:all -y &&
ddev drush cim -y &&
ddev drush pmu config_readonly -y &&
ddev drush cset search_api.server.opensearch backend_config.connector_config.url https://beis-primary-authority-register.ddev.site:9201 -y &&
ddev drush cset system.performance css.preprocess false -y &&
ddev drush cset system.performance js.preprocess false -y &&
ddev drush cr &&
ddev drush uli
