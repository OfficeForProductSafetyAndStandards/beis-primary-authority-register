# Monitoring the infrastructure

The Primary Authority Register and the core backing services are hosted with GOV.UK PaaS.

Monitoring of app metrics and logs requires a PaaS account.

## Application

The core application is deployed as a Cloud Foundry application. Metrics and logs can be obtained using the [cf-cli](https://docs.cloudfoundry.org/cf-cli/install-go-cli.html)

### Access metrics

To show basic metrics such as cpu, memory and disk space usage run.

```
cf app APP-NAME
```

The application does not collect or export metrics to any external monitoring services. Although this can be configured to export to prometheus if required.

> More information on [application metrics and monitoring](https://docs.cloud.service.gov.uk/monitoring_apps.html#app-metrics)

### Access logs

Cloud Foundry streams a limited amount of logs to your terminal for a defined time to examine logs in real time.

```
cf logs APP_NAME --recent
```

The application is not configured to keep the logs for any length of time or to push these to an external log management source. Although this can be configured

> More information on [application logs](https://docs.cloud.service.gov.uk/monitoring_apps.html#logs)

## Backing services

The key backing services hosted on GOV.UK PaaS:
* postgres database
* opensearch engine
* redis cache

Logs and metrics for each of these are exposed in the GOV.UK PaaS admin tool.

> More information on [monitoring backing services](https://docs.cloud.service.gov.uk/monitoring_services.html#monitoring-backing-services)
