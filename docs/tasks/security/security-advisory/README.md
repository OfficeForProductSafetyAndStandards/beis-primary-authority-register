# Drupal security advisory

In order to maintain a secure service the [Drupal security advisory](https://www.drupal.org/security) publishes known exploits and the required remediations.

### Check if security updates are needed

It is important that this is checked regularly to ensure there aren't any critical patches to be released.

There are a number of ways to keep up-to-date with these updates including subscribing to the 'security newsletter' by [creating a Drupal.org profile](https://www.drupal.org/user/register).

To identify all packages that have security vulnerabilities that need patching run (from `web` directory inside docker container):
```
composer audit
```

## Remediations

Any issues that are known to affect the Primary Authority Register should be patched by updating to the latest version of the affected modules or dependencies.

> See Dependency Management and Software Updates
