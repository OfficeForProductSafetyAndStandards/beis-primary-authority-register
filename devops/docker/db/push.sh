#!/bin/bash
# This script will push local assets to an environment.
# Usage: ./build.sh -t v1.0.0

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset

####################################################################################
# Prerequisites - You'll need the following installed
#    AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html
####################################################################################
command -v aws >/dev/null 2>&1 || {
    echo "################################################################################################"
    echo >&2 "Please install AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html"
    echo "If you set it up in a Python virtual env, you may need to run workon"
    echo "################################################################################################"
    exit 1
}

####################################################################################
# Set required parameters
#    TAG (required) - the semver tag to deploy
#    AWS_KEY (required) - the user deploying the script
####################################################################################
OPTIONS=t:u:s
LONGOPTS=tag:,key:,skip-download

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
IMAGE=${IMAGE:=db}
TAG=${TAG:-}
AWS_KEY=${AWS_KEY:-}
DATABASE_FILE=${DATABASE_FILE:="db-dump-production-seed.sql"}
DOWNLOAD_DATABASE=${DOWNLOAD_DATABASE:=true}

while true; do
    case "$1" in
        -t|--tag)
            TAG="$2"
            shift 2
            ;;
        -u|--key)
            KEY="$2"
            shift 2
            ;;
        -s|--skip-download)
            DOWNLOAD_DATABASE=false
            shift 1
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

# Download the latest database file.
if [[ "$DOWNLOAD_DATABASE" = true ]]; then
  SEED_DATABASE='db-dump-production-seed'
  printf "Downloading the latest seed database s3://beis-par-artifacts/builds/$SEED_DATABASE-latest.tar.gz...\n"
  aws s3 cp s3://beis-par-artifacts/backups/$SEED_DATABASE-latest.tar.gz /tmp/
  tar -zxvf /tmp/$SEED_DATABASE-latest.tar.gz -C $WORKING_DIR
fi

# Check the database seed file exists.
if [[ ! -f "$WORKING_DIR/$DATABASE_FILE" ]]; then
    printf "Error: cannot find a database to build this image with: $DATABASE_FILE\n"
    exit 1
fi

# Require a tag.
re='^v(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)';
if [[ -z "${TAG}" ]] || [[ ! $TAG =~ $re ]]; then
    printf "Error: please specify a valid semver tag (starting with a v) using the '-t' flag...\n"
    exit 2
fi

# Build the image and tag the image with the 'latest' tag.
printf "Building the image...\n"
docker build --no-cache -t $DOCKER_USER/$IMAGE:latest $WORKING_DIR --build-arg DATABASE_FILE=$DATABASE_FILE

# Also tag the image with the appropriate semver tag.
printf "Tagging the image...\n"
docker tag $DOCKER_USER/$IMAGE:latest $DOCKER_USER/$IMAGE:$TAG

# Push the image.
printf "Pushing the image...\n"
docker push $DOCKER_USER/$IMAGE:latest
docker push $DOCKER_USER/$IMAGE:$TAG
