#!/bin/bash
# This script will push release information to github.
# Usage: ./github.release.sh -t v1.0.0 $INFO

echo $BASH_VERSION

set -o errexit -euo pipefail -o noclobber -o nounset


####################################################################################
# Prerequisites - You'll need the following installed
#    Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html
####################################################################################
! getopt --test > /dev/null
if [[ ${PIPESTATUS[0]} -ne 4 ]]; then
    echo "################################################################################################"
    echo >&2 'Error: `getopt --test` failed in this environment.'
    echo "################################################################################################"

    exit 1
fi

command -v curl >/dev/null 2>&1 || {
    echo "################################################################################################"
    echo >&2 "Please install Curl"
    echo "################################################################################################"
    exit 1
}

####################################################################################
# Set required parameters
#    INFO (required) - the password for the user account
####################################################################################
OPTIONS=t:o:r:s
LONGOPTS=tag:,owner:,repo:,superseed

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
GITHUB_OWNER=${GITHUB_OWNER:="UKGovernmentBEIS"}
GITHUB_REPO=${GITHUB_REPO:="beis-primary-authority-register"}
SUPERSEED=false

while true; do
    case "$1" in
        -t|--tag)
            TAG="$2"
            shift 2
            ;;
        -o|--owner)
            GITHUB_OWNER="$2"
            shift 2
            ;;
        -r|--repo)
            GITHUB_REPO="$2"
            shift 2
            ;;
        -s|--superseed)
            SUPERSEED=true
            shift
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

## Ensure an environment has been passed
if [[ $# -ne 1 ]]; then
    echo "Please specify the content of the release notes."
    exit 4
fi
INFO=$1

if [ -z "$TAG" ]; then
    echo 'The tag is missing, please include --tag vX.X.X' >&2
    exit 1
fi

TMP_FILE=${TMP_FILE:="/tmp/github_release_${TAG}_info.txt"}
API_ENDPOINT="https://api.github.com/repos/$GITHUB_OWNER/$GITHUB_REPO/releases/tags/$TAG"

# Reset the tmp file
rm -f $TMP_FILE

####################################################################################
# Get the github release.
####################################################################################
printf "Checking for a valid resource...\n"

RELEASE=`curl -s -o response.txt -w "%{http_code}" -H "Accept: application/vnd.github.v3+json" $API_ENDPOINT`
if [ "$TAG" != 200 ]; then
    printf "Release not found, creating a new release...\n"
    # @TODO Actually create a release.
#     curl \
#      -X POST \
#      -H "Accept: application/vnd.github.v3+json" \
#      https://api.github.com/repos/octocat/hello-world/releases \
#      -d '{"tag_name":"tag_name"}'
fi

GITHUB_RELEASE=`curl -s -H "Accept: application/vnd.github.v3+json" \
    $API_ENDPOINT`

GITHUB_RELEASE_URL=`echo $GITHUB_RELEASE | jq -r '.url'`

# Append information to existing release notes.
if [ "$SUPERSEED" != true ]; then
  echo $GITHUB_RELEASE | jq -r '.body' > $TMP_FILE
fi

####################################################################################
# Update the release notes.
####################################################################################
printf "$INFO\n\n" >> $TMP_FILE

####################################################################################
# Update the release.
####################################################################################
printf "Updating the release...\n"
printf "$GITHUB_RELEASE_URL\n"
BODY=$(cat $TMP_FILE)

curl -X PATCH -H "Accept: application/vnd.github.v3+json" \
    $GITHUB_RELEASE_URL \
    -d "{\"tag_name\":\"$TAG\",\"name\":\"$TAG\",\"body\":\"$BODY\"}"
