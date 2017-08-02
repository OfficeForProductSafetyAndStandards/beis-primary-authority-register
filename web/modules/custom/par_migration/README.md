# PAR Migration
This module is intended to provide all the information and scripts for migrating data from the PAR2 database into this site.

## Source
The source for all the data is the PAR3 transition database. This is an MS SQL Server RDS database.

We require the Drupal driver for SQL Server module version 2.x to connect to this database - https://www.drupal.org/project/sqlsrv

This module has a dependency on a paid PHP MSSQL library (requires access to the private repository) - http://www.drupalonwindows.com/en/content/phpmssql

This library has a dependency on the Microsoft PDO for Linux Driver (this is the only dependency not installed or configured by composer) - https://www.microsoft.com/en-us/sql-server/developer-get-started/php/ubuntu/

Some of these migrations can require a fair bit of memory so be sure to configure the right sqlsrv memory settings:
```
client_buffer_max_kb_size = 50240
sqlsrv.ClientBufferMaxKBSize = 50240
pdo_sqlsrv.ClientBufferMaxKBSize = 50240
```

### Conection details
Connect to the host `parbeta.ch8xn6fkojyi.eu-west-1.rds.amazonaws.com`:

NOTE: For sensitive credentials such as the dbname, user and password contact a member of the team.
```
$databases['par2']['default'] = array (
  'prefix' => '',
  'host' => 'parbeta.ch8xn6fkojyi.eu-west-1.rds.amazonaws.com',
  'port' => '1433',
  'namespace' => 'Drupal\\Driver\\Database\\sqlsrv',
  'driver' => 'sqlsrv',
);
```

## Migrations
The Drupal Migration module doesn't have a UI and can only be run by drush:

```
drush mi --group=PAR2
```

To roll back a migration run `drush mr MACHINE_NAME_OF_MIGRATION`

If a migration crashes or stops unexpectedly it may need to be restarted:

```
drush mst MACHINE_NAME_OF_MIGRATION
drush mrs MACHINE_NAME_OF_MIGRATION
```

For more information on common migration commands @see https://www.drupal.org/node/1561820
