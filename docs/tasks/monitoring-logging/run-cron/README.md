# Running regular or scheduled events

The Primary Authority Register relies on scheduled actions happening at regular intervals in order run critical functions like cleaning caches and sending notifications.

Cron is a utility for running events at regular intervals and helps ensure that requests are made to Drupal cron handler to run these actions.

## Running cron

Cron can be run:
* By visiting the [cron admin page](https://primary-authority.beis.gov.uk/admin/config/system/cron) as an administrative user.
* By making an http request against the cron endpoint listed on this page.
* By running `../vendor/bin/drush cron` from the `/web` directory.

The service [Uptime Robot](https://uptimerobot.com/) is also used to run cron every 5 minutes.

## Checking that cron is running regularly

In order for many of the regular events and tasks to happen within PAR it is important to confirm that the cron is being triggered at regular intervals:

1. This can be done by visiting the [cron admin page](https://primary-authority.beis.gov.uk/admin/config/system/cron) as an administrative user, and looking for the time it was last run.

2. It is also important to sign in to [Uptime Robot](https://uptimerobot.com/) and confirm that it is configured to make regular http requests against the cron endpoint.

## Debugging cron

Drupal contains a cron handler that subsequently calls all the scheduled actions within the service.

> See all [cron jobs](https://primary-authority.beis.gov.uk/admin/config/system/cron/jobs)

Logs for each running job can be accessed from the 'operations' column of this page.
