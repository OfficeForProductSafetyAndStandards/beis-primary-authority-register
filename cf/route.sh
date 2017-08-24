#!/bin/bash

echo -n "Environment name: "
read ENV

cf target -o beis-nmo-trial -s sandbox
cf create-domain beis-nmo-trial $ENV.par-beta.co.uk
cf map-route par-beta-$ENV $ENV.par-beta.co.uk
cf create-service cdn-route cdn-route par-cdn-$ENV -c '{"domain":"'$ENV'.par-beta.co.uk"}'
