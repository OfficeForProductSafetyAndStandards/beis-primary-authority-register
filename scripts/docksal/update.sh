fin drush cache:rebuild &&
fin drush state:set system.maintenance_mode 1;
fin drush cache:rebuild &&
fin drush --yes updatedb &&
fin drush --yes config:import &&
fin drush cache:rebuild &&
fin drush --yes features:import:all &&
fin drush state:set system.maintenance_mode 0 &&
fin drush cache:rebuild &&
