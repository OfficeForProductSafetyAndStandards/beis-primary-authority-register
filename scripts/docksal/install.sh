fin restart
fin composer install &&
npm install &&
npm run install-govuk-theme &&
npm run install-par-theme &&
fin drush si -y &&
fin drush @par.paas sql:drop -y &&
fin drush @par.paas sql:cli < backups/db-dump-production-sanitised.sql &&
fin drush updb -y &&
fin drush cim -y &&
fin drush pmu config_readonly -y &&
fin drush cset search_api.server.opensearch backend_config.connector_config.url https://beis-primary-authority-register.fin.site:9201 -y &&
fin drush cset system.performance css.preprocess false &&
fin drush cset system.performance js.preprocess false &&
fin drush cr &&
fin drush uli
