## Commands that must be run to update a drupal instance.
## Use as `sh ./drupal-update.sh /var/www/html`

# Pass in the root of the project.
cd ${BASH_SOURCE%/*}

echo "Current working directory is ${PWD}/web"

# Install composer packages.
composer install -v

# Install & build libraries.

# Install theme.
npm install --prefix web/themes/contrib/govuk_theme
npm run --prefix web/themes/contrib/govuk_theme gulp build

# Install test dependencies.
#npm install --prefix tests
#npm run gulp --prefix tests
