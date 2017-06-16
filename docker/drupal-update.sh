## Commands that must be run to update a drupal instance.

# Set default drush alias.
cd ${{site_destination_directory}}/web; drush site-set @site;
# Put the site in maintenance mode.
cd ${{site_destination_directory}}/web; drush sset system.maintenance_mode 1;
# Clear cache
cd ${{site_destination_directory}}/web; drush cr;
# Run db updates.
cd ${{site_destination_directory}}/web; drush updb -y;
# Import configuration twice to fix a problem with config import when new modules are added to 'core.extensions.yml'.
cd ${{site_destination_directory}}/web; drush cim -y; drush cim -y
# Take the site out of maintenance mode.
cd ${{site_destination_directory}}/web; drush sset system.maintenance_mode 0;
# Clear cache.
cd ${{site_destination_directory}}/web; drush cr;'