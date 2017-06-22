# Drupal

The web application runs on Drupal 8.

# Configure

To configure the application run the following

    npm install
    npm run gulp
    composer install
    sh ./docker/drupal-update.sh /var/www/html
    
You must run these commands every time you switch branch or change the applications configuration in any way.
