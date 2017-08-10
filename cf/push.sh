#!/bin/bash

####################################################################################
# To use this script, set up a Python virtual environment
# and set your AWS access keys
####################################################################################
# mkvirtualenv beis-par-beta
# workon beis-par-beta
# pip install --upgrade awscli
# aws --version
####################################################################################
# You can also set these in ~/.bash_profile or by running
# aws configure (Set region name to be "eu-west-1")
####################################################################################
# export AWS_ACCESS_KEY_ID=
# export AWS_SECRET_ACCESS_KEY=
####################################################################################
# You'll need to create a .env.XXXX file for each
# environment you need to be able to push to, containing
# the following keys
#
# S3_ACCESS_KEY=
# S3_SECRET_KEY=
# S3_BUCKET_PUBLIC=transform-par-beta-production-public
# S3_BUCKET_PRIVATE=transform-par-beta-production-private
# S3_BUCKET_ARTIFACTS=transform-par-beta-artifacts
# PAR_HASH_SALT=
# PAR_GOVUK_NOTIFY_KEY=
# PAR_GOVUK_NOTIFY_TEMPLATE=
####################################################################################

ENV=$1
VER=$2

rm -rf build
mkdir build
cd build
aws s3 cp s3://transform-par-beta-artifacts/builds/$VER.tar.gz .
tar -zxvf $VER.tar.gz
rm $VER.tar.gz

cf push -f manifest.$ENV.yml
cd ..
source .env.$ENV
cf set-env par-beta-$ENV S3_ACCESS_KEY $S3_ACCESS_KEY
cf set-env par-beta-$ENV S3_SECRET_KEY $S3_SECRET_KEY
cf set-env par-beta-$ENV PAR_HASH_SALT $PAR_HASH_SALT
cf set-env par-beta-$ENV S3_BUCKET_PUBLIC $S3_BUCKET_PUBLIC
cf set-env par-beta-$ENV S3_BUCKET_PRIVATE $S3_BUCKET_PRIVATE
cf set-env par-beta-$ENV S3_BUCKET_ARTIFACTS $S3_BUCKET_ARTIFACTS
cf set-env par-beta-$ENV APP_ENV $ENV
cf set-env par-beta-$ENV PAR_GOVUK_NOTIFY_KEY $PAR_GOVUK_NOTIFY_KEY
cf set-env par-beta-$ENV PAR_GOVUK_NOTIFY_TEMPLATE $PAR_GOVUK_NOTIFY_TEMPLATE
cf restage par-beta-$ENV

cf ssh par-beta-$ENV -c "cd app/tools && python post_deploy.py"
sh update-domain-$ENV.sh
