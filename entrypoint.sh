#!/bin/bash

if [[ $XDEBUG == "debug" ]]; then
    printf "Copying xdebug.on.ini file into php configuration directory...\n"
    cat ./devops/docker/xdebug.on.ini >> "${PHP_INI_DIR}/conf.d/xdebug.ini"
else
    printf "Copying xdebug.off.ini file into php configuration directory...\n"
    cat ./devops/docker/xdebug.off.ini >> "${PHP_INI_DIR}/conf.d/xdebug.ini"
fi

apachectl -D FOREGROUND
