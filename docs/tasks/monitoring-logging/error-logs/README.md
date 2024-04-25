# Application error logs

The Primary Authority Register application sends all error logs to [Sentry](https://opss.sentry.io).

**Note:** Because the integration for Sentry is at the application level and not at the CDN or web server, this will only report on `4xx` errors and not `5xx` errors.
