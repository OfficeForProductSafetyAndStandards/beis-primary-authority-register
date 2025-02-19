#!/bin/bash

if [[ $ENV == 'production' ]]; then

    echo "Resetting production to initial state..."

    printf "Restoring the database...\n"
    cf run-task $TARGET_ENV -m 2G -k 2G --name DB_RESTORE -c "./scripts/drop.sh && \
        cd $REMOTE_BUILD_DIR/web && \
        tar --no-same-owner -zxvf $REMOTE_BUILD_DIR/$DB_DIR/$DB_NAME.tar.gz -C $REMOTE_BUILD_DIR/$DB_DIR && \
        drush @par.paas sql:cli < $REMOTE_BUILD_DIR/$DB_DIR/$DB_NAME.sql && \
        rm -f $REMOTE_BUILD_DIR/$DB_DIR/$DB_NAME.sql"

    # Wait for database to be imported.
    cf_poll_task $TARGET_ENV DB_IMPORT
    printf "Database imported...\n"

    printf "Pushing initial release code...\n"
    cf push --no-start -f $MANIFEST -p $BUILD_DIR --var app=$TARGET_ENV $TARGET_ENV

    echo "Production reset complete."
  ;;
    echo "Production reset cancelled."
    exit 12
  ;;
fi
