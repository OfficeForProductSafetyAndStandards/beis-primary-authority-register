rm -rf ../vendor
rm -rf ../node_modules
rm -rf ../tests/node_modules
if [ -f ../web/sites/default/settings.local.php ]; then
  rm ../web/sites/default/settings.local.php
fi

