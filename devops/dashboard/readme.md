# Primary Authority Register Dashboard

## Install dependencies

	composer install
	cd public
	yarn install

## Set environment variables

	BEIS_PAR_PUBNUB_PUBLISH_KEY
	BEIS_PAR_PUBNUB_SUBSCRIBE_KEY
	UPTIME_ROBOT_API_KEY

## Generate application key

	php artisan key:generate

## Deploy to Cloud Foundry

	cf push par-beta-dashboard
	cf set-env par-dashboard BEIS_PAR_PUBNUB_PUBLISH_KEY **********
	cf set-env par-dashboard BEIS_PAR_PUBNUB_SUBSCRIBE_KEY **********
	cf set-env par-dashboard UPTIME_ROBOT_API_KEY **********
	cf set-env par-dashboard APP_KEY **********
	cf restage par-dashboard
