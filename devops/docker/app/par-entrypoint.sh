#!/bin/bash
set -e

if [[ $XDEBUG == "debug" ]]; then
    printf "Copying xdebug.ini file into php configuration directory...\n"
    cp /home/php/conf.d/xdebug.on.ini "${PHP_INI_DIR}/conf.d/xdebug.ini"
else
    printf "Copying xdebug.ini file into php configuration directory...\n"
    cp /home/php/conf.d/xdebug.off.ini "${PHP_INI_DIR}/conf.d/xdebug.ini"
fi

apachectl -D FOREGROUND
