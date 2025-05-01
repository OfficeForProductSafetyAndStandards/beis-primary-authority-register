#!/bin/bash
# This script will push local assets to an environment.
# Usage: ./push.sh -t v1.0.0

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

####################################################################################
# Set required parameters
#    TAG (required) - the semver tag to deploy
####################################################################################
OPTIONS=t:
LONGOPTS=tag:

# -use ! and PIPESTATUS to get exit code with errexit set
# -temporarily store output to be able to check for errors
# -activate quoting/enhanced mode (e.g. by writing out “--options”)
# -pass arguments only via   -- "$@"   to separate them correctly
! PARSED=$(getopt --options=$OPTIONS --longoptions=$LONGOPTS --name "$0" -- "$@")
if [[ ${PIPESTATUS[0]} -ne 0 ]]; then
    # e.g. return value is 1
    #  then getopt has complained about wrong arguments to stdout
    exit 2
fi
# read getopt’s output this way to handle the quoting right:
eval set -- "$PARSED"

# Defaults
DOCKER_USER=${DOCKER_USER:=beispar}
IMAGE=${IMAGE:=web}
TAG=${TAG:-}

while true; do
    case "$1" in
        -t|--tag)
            TAG="$2"
            shift 2
            ;;
        --)
            shift
            break
            ;;
        *)
            echo "Programming error"
            exit 3
            ;;
    esac
done

# Operate in the docker directory.
WORKING_DIR=${BASH_SOURCE%/*}
printf "Working directory: $WORKING_DIR\n"

# Require a tag.
re='^(v(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)|latest)';
if [[ -z "${TAG}" ]] || [[ ! $TAG =~ $re ]]; then
    printf "Error: please specify a valid semver tag (starting with a v) using the '-t' flag...\n"
    exit 2
fi

# Build the image and tag the image with the 'latest' tag.
printf "Building the image...\n"
docker build --no-cache -t $DOCKER_USER/$IMAGE:latest $WORKING_DIR

# Also tag the image with the appropriate semver tag.
printf "Tagging the image...\n"
docker tag $DOCKER_USER/$IMAGE:latest $DOCKER_USER/$IMAGE:$TAG

# Push the image.
printf "Pushing the image...\n"
docker push $DOCKER_USER/$IMAGE:latest
docker push $DOCKER_USER/$IMAGE:$TAG
