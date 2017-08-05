#!/bin/bash
docker exec -i par_beta_web sh /var/www/html/drupal-stub-data.sh /var/www/html
docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio --spec src/features/user-journey-change-partnership-detail.feature wdio.BUILD.conf.js"
docker exec -i par_beta_web sh /var/www/html/drupal-stub-data.sh /var/www/html
docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio --spec src/features/user-journey-documentation.feature wdio.BUILD.conf.js"
docker exec -i par_beta_web sh /var/www/html/drupal-stub-data.sh /var/www/html
docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio --spec src/features/user-journey-inspection-plans.feature wdio.BUILD.conf.js"
docker exec -i par_beta_web sh /var/www/html/drupal-stub-data.sh /var/www/html
docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio --spec src/features/user-journey-send-invite.feature wdio.BUILD.conf.js"
docker exec -i par_beta_web sh /var/www/html/drupal-stub-data.sh /var/www/html
docker exec -ti par_beta_web bash -c "cd tests && ./node_modules/.bin/wdio --spec src/features/user-journey-business.feature wdio.BUILD.conf.js"
docker exec -i par_beta_web sh /var/www/html/drupal-stub-data.sh /var/www/html