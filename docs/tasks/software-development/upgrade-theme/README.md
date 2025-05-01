# Upgrade the design system

In order to ensure the GOVUK Design System is up-to-date:

1. Is the latest release of the Drupal Theme being used?

2. Is this theme using the latest version of the Design System?

> See [Staying up to date with changes](https://frontend.design-system.service.gov.uk/staying-up-to-date/#staying-up-to-date-with-changes)

## Drupal Theme

Primary Authority uses the [GOV.UK Theme](https://www.drupal.org/project/govuk_theme) to adhere to the GDS Design Guide.

This is a contributed (third-party) theme for Drupal that is updated through dependency management.

It is declared as `drupal/govuk_theme` in the root `composer.json` file. The semver version constraint must match the latest release listed on the [project page](https://www.drupal.org/project/govuk_theme).

> See Dependency Management and Software Updates

## Design System

Primary Authority uses the [GOVUK Design System](https://design-system.service.gov.uk/) provided by GDS to ensure consistency with other government services and to adhere to accessibility and best practice design standards.

The Design System is imported into the Drupal Theme as an npm dependency.

It is declared as `govuk-frontend` in theme `package.json`. The semver version constraint must match the latest release listed on the [ releases page](frohttps://github.com/alphagov/govuk-frontend/releasesntend)

Because the Drupal Theme is a third-party package, an issue must be opened in the [issue queue](https://www.drupal.org/project/issues/govuk_theme), a pull request made with the required changes, and a request made to the maintainers to review these changes.

> See Updating Contributed Modules and Themes
