# Application configuration

Drupal has a robust system for managing the application configuration.

It uses a concept of 'active' configuration, which is the stored in the database for improved performance, and 'staged' configuration, which is stored in the filesystem for improved manageability and the ability to revision changes through git.

> See [managing configuration](https://www.drupal.org/docs/administering-a-drupal-site/configuration-management/managing-your-sites-configuration)

## Active configuration

Active configuration is stored in the database, and managed through the administrative UI that Drupal provides.

Configuration is always deployed with the code, so any changes made to the active configuration must be exported to the staged configuration in order to be deployed. From the `/web` directory run:

```
../vendor/bin/drush config:export
```

## Staged configuration

Staged configuration is stored in yaml files in the `/sync` directory.

Any changes made to these files must be imported into the site before they will take effect. From the `/web` directory run:
```
../vendor/bin/drush config:import
```

## Make changes to the configuration

Only the staged configuration is deployed to higher environments, changes made to the staged configuration must be committed to git and deployed through the usual process.

The easiest and safest way, however, to make changes to the configuration is to start a local instance of the website, sign in as the administrator account, and make the necessary changes through the Administrative UI that Drupal provides.

This ensures that invalid configuration cannot be applied. But it does require the active configuration be exported to the staged config.