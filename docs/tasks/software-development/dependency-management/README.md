# Dependency Management

Dependencies are managed for PHP by composer, and for node by npm.

## Composer

[Composer](https://getcomposer.org/) is the PHP dependency management tool.

Dependencies managed by php include Drupal, and all of the contributed modules, PHPUnit, for running unit testing, and Drush, the command line utility for interacting with Drupal.

All dependencies are defined in the root `composer.json` file, which uses a typical semver pattern to list versions and constraints.

These versions are then locked in place in the `composer.lock` file, and installed in the root `/vendor` directory.

### Update dependencies

To update dependencies within the existing semver constraints run `composer update` and commit the changes to the `composer.lock` file.

To update dependencies to a version outside of the existing semver version constraints, the `composer.json` file must first be updated to the new set of constraints.

## NPM

[NPM](https://www.npmjs.com/) is the node / javascript package manager.

Dependencies managed by npm include GOVUK Design System, and gulp, which is used to run scripts in javascript and build the theme assets.

All dependencies are defined in the root `package.json` file, or in the `package.json` files inside contributed modules or themes. Each `package.json` file uses a typical semver pattern to list versions and constraints.

These versions are then locked in place in the corresponding `package.lock` file, and installed in the `node_modules` directory relative to the `package.lock` file.

### Update root dependencies

To update project level dependencies within the existing semver constraints run update the dependencies and commit any changes to the `package.lock` file:
```
npm update
```

To update dependencies to a version outside of the existing semver version constraints, the `package.json` file must first be updated to the new set of constraints.

### Update contributed project dependencies

The `package.lock` file for contributed projects is not committed, instead every time the project dependencies are installed the latest versions within the constraints are downloaded.

When deploying to production the latest allowed version will always be installed and compliled: 
```
npm run frontend
```

When building locally on a persistent environment, the local dependencies must first be removed to get the latest versions:
```
npm run rebuild-frontend
```

To update dependencies to a version outside of the existing semver version constraints, the `package.json` file must first be updated to the new set of constraints. For contributed projects this requires extra considerations.

> See Updating Contributed Modules and Themes 
