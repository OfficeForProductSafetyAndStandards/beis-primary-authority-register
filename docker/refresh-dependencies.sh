#!/usr/bin/env bash

# Run this script whenever you switch branches

PRECOMMAND=""

case "$(uname -s)" in

   Darwin)
     echo 'Mac OS X'
     ;;

   Linux)
     echo 'Linux'
     ;;

   CYGWIN*|MINGW32*|MINGW64*|MSYS*)
     echo 'MS Windows'
     dos2unix ../drupal-update.sh
     PRECOMMAND="winpty"
     ;;

   *)
     echo 'other OS' 
     ;;
esac

# Install dependencies

    $PRECOMMAND docker exec -ti par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'

# Install front end dependencies

    $PRECOMMAND docker exec -it par_beta_web bash -c "rm -rf node_modules && ../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    $PRECOMMAND docker exec -it par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm run gulp"
    
# Update Drupal
    
    $PRECOMMAND docker exec -it par_beta_web bash -c "sh drupal-update.sh /var/www/html"





