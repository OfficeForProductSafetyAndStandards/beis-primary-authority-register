---
title: Runbook
last_reviewed_on: 2024-01-27
review_in: 6 months
---

# Team Runbook

The Primary Authority Register is a service which helps business receive advice and guidance from local authorities to meet regulatory requirements.

This is based on legal partnerships between business and individual local authorities and a framework that enforces businesses to meet the advice issued to them.

For more information see [What is Primary Authority?](https://www.gov.uk/guidance/local-regulation-primary-authority#what-is-primary-authority)

## Regular Activities

* Check dependencies are up to date - monthly
* Check errors are being logged - monthly
* Check uptime monitor is operational - monthly
* Check cron is running - monthly

## Tasks

This runbook will describe the following topic areas and how to accomplish key tasks within each.

### Software Development

* Setup a local environment
* Making changes to the code
* Making changes to drupal contributed modules
* Updating the GDS Design System
* Dependency management and software updates

### Configuration

* Change application settings
* Secret management

### Version Control, CI and Deployment

* Using pull requests
* Provision and destroy test environments
* Check current release
* Deploy a new release
* Debug deployments through CI
* Add a new automated task to CI
* Writing release notes

### Testing

* Writing unit tests
* Writing feature tests
* Running performance tests
* Perform manual testing on environments

### Hosting and Infrastructure

* Understanding the architecture
* Understand the GOVUK PaaS responsibility model
* Update the buildpack version
* Upgrade php version 
* Manage backing services
* Backup the database
* Sanitise the database
* Restore the database
* Rebuild stale caches
* Rebuild search indexes

### Logging, monitoring and alerts

* Check uptime alerts are functioning
* Check cron is running
* View error logs
* Monitoring service status
* Check opensearch index

### Security

* Check software versions are up-to-date
* Check Drupal security advisory
* Infrastructure upgrades

### Operating the service

* Disaster recovery
* Incident response and incident records