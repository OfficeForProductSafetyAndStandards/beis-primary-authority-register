#!/bin/bash

set -o errexit -euo pipefail -o noclobber -o nounset

cf env beis-par-production | \
  awk '/environment_variables/{print; getline; while (match($0, /^[[:space:]]*".*":/)) {gsub(/[",]/, "", $0); gsub(/^[[:space:]]*{/, "", $0); gsub(/}$/, "", $0); split($0, a, ":"); printf "%s=%s\n", a[1], a[2]; getline;}}' | \
  sed 's/^[[:space:]]*//;s/[[:space:]]*$//' > ${BASH_SOURCE%/*}/../.env
