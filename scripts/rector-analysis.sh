# Split into modules and themes to reduce timeout chances.",
vendor/bin/rector --dry-run --no-progress-bar --config=./tests/rector/rector.php process web/themes/custom;
vendor/bin/rector --dry-run --no-progress-bar --config=./tests/rector/rector.php process web/modules/custom;
