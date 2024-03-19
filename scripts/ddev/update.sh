ddev drush cr &&
ddev drush state:set system.maintenance_mode 1;
ddev drush cr &&
ddev drush updb -y &&
ddev drush cim -y &&
ddev drush cr &&
ddev drush features:import:all -y &&
ddev drush state:set system.maintenance_mode 0 &&
ddev drush cr &&
