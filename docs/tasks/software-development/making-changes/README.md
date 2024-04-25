# Making Changes

Changes must be made through git, using feature branches, with a merge request opened against the master branch.

Checks and tests will be run on the merge request before it can be committed.

## /web

All files related to the application sit within this directory.

As an extensible software framework, Drupal follows a common directory structure for all projects.

### [Drupal Directory Structure](https://www.drupal.org/docs/understanding-drupal/directory-structure)

* **/core** - All the files required to run the Drupal application
* **/modules/contrib** - All the community contributed modules that extend Drupal's functionality
* **/modules/custom** - All the custom modules that have been writted specifically for PAR. For the most part, this is where the business logic is contained
* **/modules/features** - All the configuration modules that define the user journeys within PAR.
* **/sites/default** - The location of all settings and configuration logic, specifically for determining how ENV variables are mapped to application configuration.

> See Making changes to contributed modules