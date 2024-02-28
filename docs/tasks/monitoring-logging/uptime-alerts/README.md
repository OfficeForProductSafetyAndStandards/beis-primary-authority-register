# Uptime Monitoring

The service [Uptime Robot](https://uptimerobot.com/) is used to monitor the site health and report on any downtime.

## Health check

The Primary Authority Register has a health check endpoint at https://primary-authority.beis.gov.uk/health

It will report a `200` http status code if the service is ok, or a `4xx` status code for any downtime.

Uptime robot checks this endpoint every minute to detect the health of the service.