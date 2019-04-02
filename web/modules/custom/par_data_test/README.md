# PAR Data Test 
This module stores all the data required by the end-to-end tests.

It uses the default_content module to import and export this data into a format that can be managed by the test suite. See the default content documentation - https://www.drupal.org/docs/8/modules/default-content

## Exporting data
The first step to exporting data for use in tests is to create the data. This can be done using the regular user facing journeys that have been created for PAR, for example applying for a partnership or raising an enforcement notice.

Once the data has been created it can be exported with the drush command `drush dcer {entity_type} {entity_id} --folder=module/custom/par_data_test/content/`, more details on the drush commands can be found in the documentation above.
**Note:** Drush commands must be run from within the web root `cd /var/www/par/web/ && ../vendor/bin/drush ...`

### Caveats
* There are some rare cases where data items that cannot yet be created through PAR, such as new authorities. The best way to create these is through the admin links in the site - /admin/content/par_data/par_data_authority/add
* The command `drush dcer` exports all connected data items along with the original item this typically includes the default drupal user `dadmin`, this user should never be comitted, ignore this file.
* Users can only be half exported using the drush commands. Sensitive and privileged information such as e-mail address, password, and user roles is typically ignored and must me edited manually once the file has been exported (see an existing user item to compare).

## Data structure
Each test feature should have it's own data items, such that it can run independently or in parallel with any other features, and any data it updates will not have any effect on other test features.
