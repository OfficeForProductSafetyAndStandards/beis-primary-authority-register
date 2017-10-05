<?php
$root_path = dirname(__DIR__) . '/../../';
require "{$root_path}/vendor/autoload.php";
if (file_exists($root_path . '.env')) {
    $dotenv = new Dotenv\Dotenv($root_path);
    $dotenv->load();
}

/**
 * @file
 * Drupal site-specific configuration file.
 *
 * IMPORTANT NOTE:
 * This file may have been set to read-only by the Drupal installation program.
 * If you make changes to this file, be sure to protect it again after making
 * your modifications. Failure to remove write permissions to this file is a
 * security risk.
 *
 * In order to use the selection rules below the multisite aliasing file named
 * sites/sites.php must be present. Its optional settings will be loaded, and
 * the aliases in the array $sites will override the default directory rules
 * below. See sites/example.sites.php for more information about aliases.
 *
 * The configuration directory will be discovered by stripping the website's
 * hostname from left to right and pathname from right to left. The first
 * configuration file found will be used and any others will be ignored. If no
 * other configuration file is found then the default configuration file at
 * 'sites/default' will be used.
 *
 * For example, for a fictitious site installed at
 * https://www.drupal.org:8080/mysite/test/, the 'settings.php' file is searched
 * for in the following directories:
 *
 * - sites/8080.www.drupal.org.mysite.test
 * - sites/www.drupal.org.mysite.test
 * - sites/drupal.org.mysite.test
 * - sites/org.mysite.test
 *
 * - sites/8080.www.drupal.org.mysite
 * - sites/www.drupal.org.mysite
 * - sites/drupal.org.mysite
 * - sites/org.mysite
 *
 * - sites/8080.www.drupal.org
 * - sites/www.drupal.org
 * - sites/drupal.org
 * - sites/org
 *
 * - sites/default
 *
 * Note that if you are installing on a non-standard port number, prefix the
 * hostname with that number. For example,
 * https://www.drupal.org:8080/mysite/test/ could be loaded from
 * sites/8080.www.drupal.org.mysite.test/.
 *
 * @see example.sites.php
 * @see \Drupal\Core\DrupalKernel::getSitePath()
 *
 * In addition to customizing application settings through variables in
 * settings.php, you can create a services.yml file in the same directory to
 * register custom, site-specific service definitions and/or swap out default
 * implementations with custom ones.
 */

/**
 * Database settings:
 *
 * The $databases array specifies the database connection or
 * connections that Drupal may use.  Drupal is able to connect
 * to multiple databases, including multiple types of databases,
 * during the same request.
 *
 * One example of the simplest connection array is shown below. To use the
 * sample settings, copy and uncomment the code below between the @code and
 * @endcode lines and paste it after the $databases declaration. You will need
 * to replace the database username and password and possibly the host and port
 * with the appropriate credentials for your database system.
 *
 * The next section describes how to customize the $databases array for more
 * specific needs.
 *
 * @code
 * $databases['default']['default'] = array (
 *   'database' => 'databasename',
 *   'username' => 'sqlusername',
 *   'password' => 'sqlpassword',
 *   'host' => 'localhost',
 *   'port' => '3306',
 *   'driver' => 'mysql',
 *   'prefix' => '',
 *   'collation' => 'utf8mb4_general_ci',
 * );
 * @endcode
 */
 $databases = array();

/**
 * Customizing database settings.
 *
 * Many of the values of the $databases array can be customized for your
 * particular database system. Refer to the sample in the section above as a
 * starting point.
 *
 * The "driver" property indicates what Drupal database driver the
 * connection should use.  This is usually the same as the name of the
 * database type, such as mysql or sqlite, but not always.  The other
 * properties will vary depending on the driver.  For SQLite, you must
 * specify a database file name in a directory that is writable by the
 * webserver.  For most other drivers, you must specify a
 * username, password, host, and database name.
 *
 * Transaction support is enabled by default for all drivers that support it,
 * including MySQL. To explicitly disable it, set the 'transactions' key to
 * FALSE.
 * Note that some configurations of MySQL, such as the MyISAM engine, don't
 * support it and will proceed silently even if enabled. If you experience
 * transaction related crashes with such configuration, set the 'transactions'
 * key to FALSE.
 *
 * For each database, you may optionally specify multiple "target" databases.
 * A target database allows Drupal to try to send certain queries to a
 * different database if it can but fall back to the default connection if not.
 * That is useful for primary/replica replication, as Drupal may try to connect
 * to a replica server when appropriate and if one is not available will simply
 * fall back to the single primary server (The terms primary/replica are
 * traditionally referred to as master/slave in database server documentation).
 *
 * The general format for the $databases array is as follows:
 * @code
 * $databases['default']['default'] = $info_array;
 * $databases['default']['replica'][] = $info_array;
 * $databases['default']['replica'][] = $info_array;
 * $databases['extra']['default'] = $info_array;
 * @endcode
 *
 * In the above example, $info_array is an array of settings described above.
 * The first line sets a "default" database that has one primary database
 * (the second level default).  The second and third lines create an array
 * of potential replica databases.  Drupal will select one at random for a given
 * request as needed.  The fourth line creates a new database with a name of
 * "extra".
 *
 * You can optionally set prefixes for some or all database table names
 * by using the 'prefix' setting. If a prefix is specified, the table
 * name will be prepended with its value. Be sure to use valid database
 * characters only, usually alphanumeric and underscore. If no prefixes
 * are desired, leave it as an empty string ''.
 *
 * To have all database names prefixed, set 'prefix' as a string:
 * @code
 *   'prefix' => 'main_',
 * @endcode
 *
 * Per-table prefixes are deprecated as of Drupal 8.2, and will be removed in
 * Drupal 9.0. After that, only a single prefix for all tables will be
 * supported.
 *
 * To provide prefixes for specific tables, set 'prefix' as an array.
 * The array's keys are the table names and the values are the prefixes.
 * The 'default' element is mandatory and holds the prefix for any tables
 * not specified elsewhere in the array. Example:
 * @code
 *   'prefix' => array(
 *     'default'   => 'main_',
 *     'users'     => 'shared_',
 *     'sessions'  => 'shared_',
 *     'role'      => 'shared_',
 *     'authmap'   => 'shared_',
 *   ),
 * @endcode
 * You can also use a reference to a schema/database as a prefix. This may be
 * useful if your Drupal installation exists in a schema that is not the default
 * or you want to access several databases from the same code base at the same
 * time.
 * Example:
 * @code
 *   'prefix' => array(
 *     'default'   => 'main.',
 *     'users'     => 'shared.',
 *     'sessions'  => 'shared.',
 *     'role'      => 'shared.',
 *     'authmap'   => 'shared.',
 *   );
 * @endcode
 * NOTE: MySQL and SQLite's definition of a schema is a database.
 *
 * Advanced users can add or override initial commands to execute when
 * connecting to the database server, as well as PDO connection settings. For
 * example, to enable MySQL SELECT queries to exceed the max_join_size system
 * variable, and to reduce the database connection timeout to 5 seconds:
 * @code
 * $databases['default']['default'] = array(
 *   'init_commands' => array(
 *     'big_selects' => 'SET SQL_BIG_SELECTS=1',
 *   ),
 *   'pdo' => array(
 *     PDO::ATTR_TIMEOUT => 5,
 *   ),
 * );
 * @endcode
 *
 * WARNING: The above defaults are designed for database portability. Changing
 * them may cause unexpected behavior, including potential data loss. See
 * https://www.drupal.org/developing/api/database/configuration for more
 * information on these defaults and the potential issues.
 *
 * More details can be found in the constructor methods for each driver:
 * - \Drupal\Core\Database\Driver\mysql\Connection::__construct()
 * - \Drupal\Core\Database\Driver\pgsql\Connection::__construct()
 * - \Drupal\Core\Database\Driver\sqlite\Connection::__construct()
 *
 * Sample Database configuration format for PostgreSQL (pgsql):
 * @code
 *   $databases['default']['default'] = array(
 *     'driver' => 'pgsql',
 *     'database' => 'databasename',
 *     'username' => 'sqlusername',
 *     'password' => 'sqlpassword',
 *     'host' => 'localhost',
 *     'prefix' => '',
 *   );
 * @endcode
 *
 * Sample Database configuration format for SQLite (sqlite):
 * @code
 *   $databases['default']['default'] = array(
 *     'driver' => 'sqlite',
 *     'database' => '/path/to/databasefilename',
 *   );
 * @endcode
 */

/**
 * Location of the site configuration files.
 *
 * The $config_directories array specifies the location of file system
 * directories used for configuration data. On install, the "sync" directory is
 * created. This is used for configuration imports. The "active" directory is
 * not created by default since the default storage for active configuration is
 * the database rather than the file system. (This can be changed. See "Active
 * configuration settings" below).
 *
 * The default location for the "sync" directory is inside a randomly-named
 * directory in the public files path. The setting below allows you to override
 * the "sync" location.
 *
 * If you use files for the "active" configuration, you can tell the
 * Configuration system where this directory is located by adding an entry with
 * array key CONFIG_ACTIVE_DIRECTORY.
 *
 * Example:
 * @code
 *   $config_directories = array(
 *     CONFIG_SYNC_DIRECTORY => '/directory/outside/webroot',
 *   );
 * @endcode
 */
$config_directories['sync'] = '../sync';

/**
 * Settings:
 *
 * $settings contains environment-specific configuration, such as the files
 * directory and reverse proxy address, and temporary configuration, such as
 * security overrides.
 *
 * @see \Drupal\Core\Site\Settings::get()
 */

/**
 * The active installation profile.
 *
 * Changing this after installation is not recommended as it changes which
 * directories are scanned during extension discovery. If this is set prior to
 * installation this value will be rewritten according to the profile selected
 * by the user.
 *
 * @see install_select_profile()
 *
 * @deprecated in Drupal 8.3.0 and will be removed before Drupal 9.0.0. The
 *   install profile is written to the core.extension configuration. If a
 *   service requires the install profile use the 'install_profile' container
 *   parameter. Functional code can use \Drupal::installProfile().
 */
$settings['install_profile'] = 'standard';

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 *
 * This variable will be set to a random value by the installer. All one-time
 * login links will be invalidated if the value is changed. Note that if your
 * site is deployed on a cluster of web servers, you must ensure that this
 * variable has the same value on each server.
 *
 * For enhanced security, you may set this variable to the contents of a file
 * outside your document root; you should also ensure that this file is not
 * stored with backups of your database.
 *
 * Example:
 * @code
 *   $settings['hash_salt'] = file_get_contents('/home/example/salt.txt');
 * @endcode
 */
$settings['hash_salt'] = getenv('PAR_HASH_SALT');

/**
 * Deployment identifier.
 *
 * Drupal's dependency injection container will be automatically invalidated and
 * rebuilt when the Drupal core version changes. When updating contributed or
 * custom code that changes the container, changing this identifier will also
 * allow the container to be invalidated as soon as code is deployed.
 */
# $settings['deployment_identifier'] = \Drupal::VERSION;

/**
 * Access control for update.php script.
 *
 * If you are updating your Drupal installation using the update.php script but
 * are not logged in using either an account with the "Administer software
 * updates" permission or the site maintenance account (the account that was
 * created during installation), you will need to modify the access check
 * statement below. Change the FALSE to a TRUE to disable the access check.
 * After finishing the upgrade, be sure to open this file again and change the
 * TRUE back to a FALSE!
 */
$settings['update_free_access'] = FALSE;

/**
 * External access proxy settings:
 *
 * If your site must access the Internet via a web proxy then you can enter the
 * proxy settings here. Set the full URL of the proxy, including the port, in
 * variables:
 * - $settings['http_client_config']['proxy']['http']: The proxy URL for HTTP
 *   requests.
 * - $settings['http_client_config']['proxy']['https']: The proxy URL for HTTPS
 *   requests.
 * You can pass in the user name and password for basic authentication in the
 * URLs in these settings.
 *
 * You can also define an array of host names that can be accessed directly,
 * bypassing the proxy, in $settings['http_client_config']['proxy']['no'].
 */
# $settings['http_client_config']['proxy']['http'] = 'http://proxy_user:proxy_pass@example.com:8080';
# $settings['http_client_config']['proxy']['https'] = 'http://proxy_user:proxy_pass@example.com:8080';
# $settings['http_client_config']['proxy']['no'] = ['127.0.0.1', 'localhost'];

/**
 * Reverse Proxy Configuration:
 *
 * Reverse proxy servers are often used to enhance the performance
 * of heavily visited sites and may also provide other site caching,
 * security, or encryption benefits. In an environment where Drupal
 * is behind a reverse proxy, the real IP address of the client should
 * be determined such that the correct client IP address is available
 * to Drupal's logging, statistics, and access management systems. In
 * the most simple scenario, the proxy server will add an
 * X-Forwarded-For header to the request that contains the client IP
 * address. However, HTTP headers are vulnerable to spoofing, where a
 * malicious client could bypass restrictions by setting the
 * X-Forwarded-For header directly. Therefore, Drupal's proxy
 * configuration requires the IP addresses of all remote proxies to be
 * specified in $settings['reverse_proxy_addresses'] to work correctly.
 *
 * Enable this setting to get Drupal to determine the client IP from
 * the X-Forwarded-For header (or $settings['reverse_proxy_header'] if set).
 * If you are unsure about this setting, do not have a reverse proxy,
 * or Drupal operates in a shared hosting environment, this setting
 * should remain commented out.
 *
 * In order for this setting to be used you must specify every possible
 * reverse proxy IP address in $settings['reverse_proxy_addresses'].
 * If a complete list of reverse proxies is not available in your
 * environment (for example, if you use a CDN) you may set the
 * $_SERVER['REMOTE_ADDR'] variable directly in settings.php.
 * Be aware, however, that it is likely that this would allow IP
 * address spoofing unless more advanced precautions are taken.
 */
$settings['reverse_proxy'] = TRUE;

/**
 * Specify every reverse proxy IP address in your environment.
 * This setting is required if $settings['reverse_proxy'] is TRUE.
 */
$settings['reverse_proxy_addresses'] = array('127.0.0.1','13.32.0.0/15','13.52.0.0/16','13.54.0.0/15','13.56.0.0/16','13.57.0.0/16','13.58.0.0/15','13.112.0.0/14','13.124.0.0/16','13.125.0.0/16','13.126.0.0/15','13.209.0.0/16','13.210.0.0/15','13.228.0.0/15','13.230.0.0/15','13.232.0.0/14','13.236.0.0/14','13.250.0.0/15','18.144.0.0/15','18.194.0.0/15','18.196.0.0/15','18.200.0.0/16','18.216.0.0/14','18.220.0.0/14','18.231.0.0/16','23.20.0.0/14','27.0.0.0/22','34.192.0.0/12','34.208.0.0/12','34.224.0.0/12','34.240.0.0/13','34.248.0.0/13','35.153.0.0/16','35.154.0.0/16','35.155.0.0/16','35.156.0.0/14','35.160.0.0/13','35.168.0.0/13','35.176.0.0/15','35.178.0.0/15','35.180.0.0/15','35.182.0.0/15','43.250.192.0/24','43.250.193.0/24','46.51.128.0/18','46.51.192.0/20','46.51.216.0/21','46.51.224.0/19','46.137.0.0/17','46.137.128.0/18','46.137.192.0/19','46.137.224.0/19','50.16.0.0/15','50.18.0.0/16','50.19.0.0/16','50.112.0.0/16','52.0.0.0/15','52.2.0.0/15','52.4.0.0/14','52.8.0.0/16','52.9.0.0/16','52.10.0.0/15','52.12.0.0/15','52.14.0.0/16','52.15.0.0/16','52.16.0.0/15','52.18.0.0/15','52.20.0.0/14','52.24.0.0/14','52.28.0.0/16','52.29.0.0/16','52.30.0.0/15','52.32.0.0/14','52.36.0.0/14','52.40.0.0/14','52.44.0.0/15','52.46.0.0/18','52.46.64.0/20','52.46.80.0/21','52.46.88.0/22','52.46.92.0/22','52.46.128.0/19','52.47.0.0/16','52.48.0.0/14','52.52.0.0/15','52.54.0.0/15','52.56.0.0/16','52.57.0.0/16','52.58.0.0/15','52.60.0.0/16','52.61.0.0/16','52.62.0.0/15','52.64.0.0/17','52.64.128.0/17','52.65.0.0/16','52.66.0.0/16','52.67.0.0/16','52.68.0.0/15','52.70.0.0/15','52.72.0.0/15','52.74.0.0/16','52.75.0.0/16','52.76.0.0/17','52.76.128.0/17','52.77.0.0/16','52.78.0.0/16','52.79.0.0/16','52.80.0.0/16','52.81.0.0/16','52.82.187.0/24','52.82.188.0/22','52.82.192.0/18','52.83.0.0/16','52.84.0.0/15','52.86.0.0/15','52.88.0.0/15','52.90.0.0/15','52.92.0.0/20','52.92.16.0/20','52.92.32.0/22','52.92.39.0/24','52.92.40.0/21','52.92.48.0/22','52.92.52.0/22','52.92.56.0/22','52.92.60.0/22','52.92.64.0/22','52.92.68.0/22','52.92.72.0/22','52.92.76.0/22','52.92.80.0/22','52.92.84.0/22','52.92.88.0/22','52.92.248.0/22','52.92.252.0/22','52.93.0.0/24','52.93.1.0/24','52.93.2.0/24','52.93.3.0/24','52.93.4.0/24','52.93.5.0/24','52.93.8.0/22','52.93.16.0/24','52.94.0.0/22','52.94.4.0/24','52.94.5.0/24','52.94.6.0/24','52.94.7.0/24','52.94.8.0/24','52.94.9.0/24','52.94.10.0/24','52.94.11.0/24','52.94.12.0/24','52.94.13.0/24','52.94.14.0/24','52.94.15.0/24','52.94.16.0/24','52.94.17.0/24','52.94.20.0/24','52.94.22.0/24','52.94.24.0/23','52.94.26.0/23','52.94.28.0/23','52.94.30.0/23','52.94.32.0/20','52.94.48.0/20','52.94.64.0/22','52.94.68.0/24','52.94.72.0/22','52.94.80.0/20','52.94.96.0/20','52.94.112.0/22','52.94.192.0/22','52.94.196.0/24','52.94.197.0/24','52.94.198.0/28','52.94.198.16/28','52.94.198.32/28','52.94.198.48/28','52.94.198.64/28','52.94.198.80/28','52.94.198.96/28','52.94.198.112/28','52.94.198.128/28','52.94.198.144/28','52.94.199.0/24','52.94.200.0/24','52.94.204.0/23','52.94.206.0/23','52.94.208.0/21','52.94.216.0/21','52.94.224.0/20','52.94.240.0/22','52.94.244.0/22','52.94.248.0/28','52.94.248.16/28','52.94.248.32/28','52.94.248.48/28','52.94.248.64/28','52.94.248.80/28','52.94.248.96/28','52.94.248.112/28','52.94.248.128/28','52.94.248.144/28','52.94.248.160/28','52.94.248.176/28','52.94.248.192/28','52.94.248.208/28','52.94.248.224/28','52.94.249.0/28','52.94.249.16/28','52.94.249.32/28','52.94.249.64/28','52.94.249.80/28','52.94.252.0/23','52.94.254.0/23','52.95.0.0/20','52.95.16.0/21','52.95.24.0/22','52.95.28.0/24','52.95.30.0/23','52.95.34.0/24','52.95.35.0/24','52.95.36.0/22','52.95.40.0/24','52.95.48.0/22','52.95.56.0/22','52.95.60.0/24','52.95.61.0/24','52.95.62.0/24','52.95.63.0/24','52.95.64.0/20','52.95.80.0/20','52.95.96.0/22','52.95.100.0/22','52.95.104.0/22','52.95.108.0/23','52.95.110.0/24','52.95.111.0/24','52.95.112.0/20','52.95.128.0/21','52.95.136.0/23','52.95.138.0/24','52.95.142.0/23','52.95.144.0/24','52.95.145.0/24','52.95.146.0/23','52.95.148.0/23','52.95.150.0/24','52.95.154.0/23','52.95.156.0/24','52.95.163.0/24','52.95.164.0/23','52.95.192.0/20','52.95.212.0/22','52.95.240.0/24','52.95.241.0/24','52.95.242.0/24','52.95.243.0/24','52.95.244.0/24','52.95.245.0/24','52.95.246.0/24','52.95.247.0/24','52.95.248.0/24','52.95.249.0/24','52.95.250.0/24','52.95.251.0/24','52.95.252.0/24','52.95.253.0/24','52.95.254.0/24','52.95.255.0/28','52.95.255.16/28','52.95.255.32/28','52.95.255.48/28','52.95.255.64/28','52.95.255.80/28','52.95.255.96/28','52.95.255.112/28','52.95.255.128/28','52.95.255.144/28','52.119.160.0/20','52.119.176.0/21','52.119.184.0/22','52.119.188.0/22','52.119.192.0/22','52.119.196.0/22','52.119.206.0/23','52.119.208.0/23','52.119.210.0/23','52.119.212.0/23','52.119.214.0/23','52.119.216.0/21','52.119.224.0/21','52.119.232.0/21','52.119.240.0/21','52.192.0.0/15','52.196.0.0/14','52.200.0.0/13','52.208.0.0/13','52.216.0.0/15','52.218.0.0/17','52.218.128.0/17','52.219.0.0/20','52.219.16.0/22','52.219.20.0/22','52.219.24.0/21','52.219.32.0/21','52.219.40.0/22','52.219.44.0/22','52.219.56.0/22','52.219.60.0/23','52.219.62.0/23','52.219.64.0/22','52.219.68.0/22','52.219.72.0/22','52.219.76.0/22','52.219.80.0/20','52.220.0.0/15','52.222.0.0/17','52.222.128.0/17','54.64.0.0/15','54.66.0.0/16','54.67.0.0/16','54.68.0.0/14','54.72.0.0/15','54.74.0.0/15','54.76.0.0/15','54.78.0.0/16','54.79.0.0/16','54.80.0.0/13','54.88.0.0/14','54.92.0.0/17','54.92.128.0/17','54.93.0.0/16','54.94.0.0/16','54.95.0.0/16','54.144.0.0/14','54.148.0.0/15','54.150.0.0/16','54.151.0.0/17','54.151.128.0/17','54.152.0.0/16','54.153.0.0/17','54.153.128.0/17','54.154.0.0/16','54.155.0.0/16','54.156.0.0/14','54.160.0.0/13','54.168.0.0/16','54.169.0.0/16','54.170.0.0/15','54.172.0.0/15','54.174.0.0/15','54.176.0.0/15','54.178.0.0/16','54.179.0.0/16','54.182.0.0/16','54.183.0.0/16','54.184.0.0/13','54.192.0.0/16','54.193.0.0/16','54.194.0.0/15','54.196.0.0/15','54.198.0.0/16','54.199.0.0/16','54.200.0.0/15','54.202.0.0/15','54.204.0.0/15','54.206.0.0/16','54.207.0.0/16','54.208.0.0/15','54.210.0.0/15','54.212.0.0/15','54.214.0.0/16','54.215.0.0/16','54.216.0.0/15','54.218.0.0/16','54.219.0.0/16','54.220.0.0/16','54.221.0.0/16','54.222.0.0/19','54.222.48.0/22','54.222.57.0/24','54.222.58.0/28','54.222.128.0/17','54.223.0.0/16','54.224.0.0/15','54.226.0.0/15','54.228.0.0/16','54.229.0.0/16','54.230.0.0/16','54.231.0.0/17','54.231.128.0/19','54.231.160.0/19','54.231.192.0/20','54.231.224.0/21','54.231.232.0/21','54.231.240.0/22','54.231.244.0/22','54.231.248.0/22','54.231.252.0/24','54.231.253.0/24','54.232.0.0/16','54.233.0.0/18','54.233.64.0/18','54.233.128.0/17','54.234.0.0/15','54.236.0.0/15','54.238.0.0/16','54.239.0.0/28','54.239.0.16/28','54.239.0.32/28','54.239.0.48/28','54.239.0.64/28','54.239.0.80/28','54.239.0.96/28','54.239.0.112/28','54.239.0.128/28','54.239.0.144/28','54.239.0.160/28','54.239.0.176/28','54.239.0.192/28','54.239.0.208/28','54.239.0.224/28','54.239.0.240/28','54.239.1.0/28','54.239.1.16/28','54.239.2.0/23','54.239.4.0/22','54.239.8.0/21','54.239.16.0/20','54.239.32.0/21','54.239.48.0/22','54.239.52.0/23','54.239.54.0/23','54.239.56.0/21','54.239.96.0/24','54.239.98.0/24','54.239.99.0/24','54.239.100.0/23','54.239.104.0/23','54.239.108.0/22','54.239.116.0/22','54.239.120.0/21','54.239.128.0/18','54.239.192.0/19','54.240.128.0/18','54.240.192.0/22','54.240.196.0/24','54.240.197.0/24','54.240.198.0/24','54.240.199.0/24','54.240.200.0/24','54.240.202.0/24','54.240.203.0/24','54.240.204.0/22','54.240.208.0/22','54.240.212.0/22','54.240.216.0/22','54.240.220.0/22','54.240.225.0/24','54.240.226.0/24','54.240.227.0/24','54.240.228.0/23','54.240.230.0/23','54.240.232.0/22','54.240.244.0/22','54.240.248.0/21','54.241.0.0/16','54.242.0.0/15','54.244.0.0/16','54.245.0.0/16','54.246.0.0/16','54.247.0.0/16','54.248.0.0/15','54.250.0.0/16','54.251.0.0/16','54.252.0.0/16','54.253.0.0/16','54.254.0.0/16','54.255.0.0/16','67.202.0.0/18','72.21.192.0/19','72.44.32.0/19','75.101.128.0/17','79.125.0.0/17','87.238.80.0/21','96.127.0.0/17','103.4.8.0/22','103.4.12.0/22','103.8.172.0/22','103.246.148.0/23','103.246.150.0/23','107.20.0.0/14','122.248.192.0/18','172.96.97.0/24','172.96.98.0/24','174.129.0.0/16','175.41.128.0/18','175.41.192.0/18','176.32.64.0/19','176.32.96.0/21','176.32.104.0/21','176.32.112.0/21','176.32.120.0/22','176.32.125.0/25','176.34.0.0/19','176.34.32.0/19','176.34.64.0/18','176.34.128.0/17','177.71.128.0/17','177.72.240.0/21','178.236.0.0/20','184.72.0.0/18','184.72.64.0/18','184.72.128.0/17','184.73.0.0/16','184.169.128.0/17','185.48.120.0/22','185.143.16.0/24','203.83.220.0/22','204.236.128.0/18','204.236.192.0/18','204.246.160.0/22','204.246.164.0/22','204.246.168.0/22','204.246.174.0/23','204.246.176.0/20','205.251.192.0/19','205.251.224.0/22','205.251.228.0/22','205.251.232.0/22','205.251.236.0/22','205.251.240.0/22','205.251.244.0/23','205.251.247.0/24','205.251.248.0/24','205.251.249.0/24','205.251.250.0/23','205.251.252.0/23','205.251.254.0/24','207.171.160.0/20','207.171.176.0/20','216.137.32.0/19','216.182.224.0/20','54.183.255.128/26','54.228.16.0/26','54.232.40.64/26','54.241.32.64/26','54.243.31.192/26','54.244.52.192/26','54.245.168.0/26','54.248.220.0/26','54.250.253.192/26','54.251.31.128/26','54.252.79.128/26','54.252.254.192/26','54.255.254.192/26','107.23.255.0/26','176.34.159.192/26','177.71.207.128/26','52.82.188.0/22','52.92.0.0/20','52.92.16.0/20','52.92.32.0/22','52.92.39.0/24','52.92.40.0/21','52.92.48.0/22','52.92.52.0/22','52.92.56.0/22','52.92.60.0/22','52.92.64.0/22','52.92.68.0/22','52.92.72.0/22','52.92.76.0/22','52.92.80.0/22','52.92.84.0/22','52.92.88.0/22','52.92.248.0/22','52.92.252.0/22','52.95.128.0/21','52.95.136.0/23','52.95.138.0/24','52.95.142.0/23','52.95.144.0/24','52.95.145.0/24','52.95.146.0/23','52.95.148.0/23','52.95.150.0/24','52.95.154.0/23','52.95.156.0/24','52.95.163.0/24','52.95.164.0/23','52.216.0.0/15','52.218.0.0/17','52.218.128.0/17','52.219.0.0/20','52.219.16.0/22','52.219.20.0/22','52.219.24.0/21','52.219.32.0/21','52.219.40.0/22','52.219.44.0/22','52.219.56.0/22','52.219.60.0/23','52.219.62.0/23','52.219.64.0/22','52.219.68.0/22','52.219.72.0/22','52.219.76.0/22','52.219.80.0/20','54.222.20.0/22','54.222.48.0/22','54.231.0.0/17','54.231.128.0/19','54.231.160.0/19','54.231.192.0/20','54.231.224.0/21','54.231.232.0/21','54.231.240.0/22','54.231.248.0/22','54.231.252.0/24','54.231.253.0/24','13.52.0.0/16','13.54.0.0/15','13.56.0.0/16','13.57.0.0/16','13.58.0.0/15','13.112.0.0/14','13.124.0.0/16','13.125.0.0/16','13.126.0.0/15','13.209.0.0/16','13.210.0.0/15','13.228.0.0/15','13.230.0.0/15','13.232.0.0/14','13.236.0.0/14','13.250.0.0/15','18.144.0.0/15','18.194.0.0/15','18.196.0.0/15','18.200.0.0/16','18.216.0.0/14','18.220.0.0/14','18.231.0.0/16','23.20.0.0/14','34.192.0.0/12','34.208.0.0/12','34.224.0.0/12','34.240.0.0/13','34.248.0.0/13','35.153.0.0/16','35.154.0.0/16','35.155.0.0/16','35.156.0.0/14','35.160.0.0/13','35.168.0.0/13','35.176.0.0/15','35.178.0.0/15','35.180.0.0/15','35.182.0.0/15','46.51.128.0/18','46.51.192.0/20','46.51.216.0/21','46.51.224.0/19','46.137.0.0/17','46.137.128.0/18','46.137.192.0/19','46.137.224.0/19','50.16.0.0/15','50.18.0.0/16','50.19.0.0/16','50.112.0.0/16','52.0.0.0/15','52.2.0.0/15','52.4.0.0/14','52.8.0.0/16','52.9.0.0/16','52.10.0.0/15','52.12.0.0/15','52.14.0.0/16','52.15.0.0/16','52.16.0.0/15','52.18.0.0/15','52.20.0.0/14','52.24.0.0/14','52.28.0.0/16','52.29.0.0/16','52.30.0.0/15','52.32.0.0/14','52.36.0.0/14','52.40.0.0/14','52.44.0.0/15','52.47.0.0/16','52.48.0.0/14','52.52.0.0/15','52.54.0.0/15','52.56.0.0/16','52.57.0.0/16','52.58.0.0/15','52.60.0.0/16','52.61.0.0/16','52.62.0.0/15','52.64.0.0/17','52.64.128.0/17','52.65.0.0/16','52.66.0.0/16','52.67.0.0/16','52.68.0.0/15','52.70.0.0/15','52.72.0.0/15','52.74.0.0/16','52.75.0.0/16','52.76.0.0/17','52.76.128.0/17','52.77.0.0/16','52.78.0.0/16','52.79.0.0/16','52.80.0.0/16','52.81.0.0/16','52.83.0.0/16','52.86.0.0/15','52.88.0.0/15','52.90.0.0/15','52.95.240.0/24','52.95.241.0/24','52.95.242.0/24','52.95.243.0/24','52.95.244.0/24','52.95.245.0/24','52.95.246.0/24','52.95.247.0/24','52.95.248.0/24','52.95.249.0/24','52.95.250.0/24','52.95.251.0/24','52.95.252.0/24','52.95.253.0/24','52.95.254.0/24','52.95.255.0/28','52.95.255.16/28','52.95.255.32/28','52.95.255.48/28','52.95.255.64/28','52.95.255.80/28','52.95.255.96/28','52.95.255.112/28','52.95.255.128/28','52.95.255.144/28','52.192.0.0/15','52.196.0.0/14','52.200.0.0/13','52.208.0.0/13','52.220.0.0/15','52.222.0.0/17','54.64.0.0/15','54.66.0.0/16','54.67.0.0/16','54.68.0.0/14','54.72.0.0/15','54.74.0.0/15','54.76.0.0/15','54.78.0.0/16','54.79.0.0/16','54.80.0.0/13','54.88.0.0/14','54.92.0.0/17','54.92.128.0/17','54.93.0.0/16','54.94.0.0/16','54.95.0.0/16','54.144.0.0/14','54.148.0.0/15','54.150.0.0/16','54.151.0.0/17','54.151.128.0/17','54.152.0.0/16','54.153.0.0/17','54.153.128.0/17','54.154.0.0/16','54.155.0.0/16','54.156.0.0/14','54.160.0.0/13','54.168.0.0/16','54.169.0.0/16','54.170.0.0/15','54.172.0.0/15','54.174.0.0/15','54.176.0.0/15','54.178.0.0/16','54.179.0.0/16','54.183.0.0/16','54.184.0.0/13','54.193.0.0/16','54.194.0.0/15','54.196.0.0/15','54.198.0.0/16','54.199.0.0/16','54.200.0.0/15','54.202.0.0/15','54.204.0.0/15','54.206.0.0/16','54.207.0.0/16','54.208.0.0/15','54.210.0.0/15','54.212.0.0/15','54.214.0.0/16','54.215.0.0/16','54.216.0.0/15','54.218.0.0/16','54.219.0.0/16','54.220.0.0/16','54.221.0.0/16','54.222.128.0/17','54.223.0.0/16','54.224.0.0/15','54.226.0.0/15','54.228.0.0/16','54.229.0.0/16','54.232.0.0/16','54.233.0.0/18','54.233.64.0/18','54.233.128.0/17','54.234.0.0/15','54.236.0.0/15','54.238.0.0/16','54.241.0.0/16','54.242.0.0/15','54.244.0.0/16','54.245.0.0/16','54.246.0.0/16','54.247.0.0/16','54.248.0.0/15','54.250.0.0/16','54.251.0.0/16','54.252.0.0/16','54.253.0.0/16','54.254.0.0/16','54.255.0.0/16','67.202.0.0/18','72.44.32.0/19','75.101.128.0/17','79.125.0.0/17','96.127.0.0/17','103.4.8.0/22','103.4.12.0/22','107.20.0.0/14','122.248.192.0/18','174.129.0.0/16','175.41.128.0/18','175.41.192.0/18','176.32.64.0/19','176.34.0.0/19','176.34.32.0/19','176.34.64.0/18','176.34.128.0/17','177.71.128.0/17','184.72.0.0/18','184.72.64.0/18','184.72.128.0/17','184.73.0.0/16','184.169.128.0/17','185.48.120.0/22','204.236.128.0/18','204.236.192.0/18','216.182.224.0/20','52.95.110.0/24','205.251.192.0/21','13.32.0.0/15','13.54.63.128/26','13.59.250.0/26','13.113.203.0/24','13.124.199.0/24','13.228.69.0/24','34.195.252.0/24','34.226.14.0/24','34.232.163.208/29','35.158.136.0/24','35.162.63.192/26','35.167.191.128/26','52.15.127.128/26','52.46.0.0/18','52.52.191.128/26','52.56.127.0/25','52.57.254.0/24','52.66.194.128/26','52.78.247.128/26','52.84.0.0/15','52.199.127.192/26','52.212.248.0/26','52.220.191.0/26','52.222.128.0/17','54.182.0.0/16','54.192.0.0/16','54.230.0.0/16','54.233.255.128/26','54.239.128.0/18','54.239.192.0/19','54.240.128.0/18','204.246.164.0/22','204.246.168.0/22','204.246.174.0/23','204.246.176.0/20','205.251.192.0/19','205.251.249.0/24','205.251.250.0/23','205.251.252.0/23','205.251.254.0/24','216.137.32.0/19');

/**
 * Set this value if your proxy server sends the client IP in a header
 * other than X-Forwarded-For.
 */
# $settings['reverse_proxy_header'] = 'X_CLUSTER_CLIENT_IP';

/**
 * Set this value if your proxy server sends the client protocol in a header
 * other than X-Forwarded-Proto.
 */
# $settings['reverse_proxy_proto_header'] = 'X_FORWARDED_PROTO';

/**
 * Set this value if your proxy server sends the client protocol in a header
 * other than X-Forwarded-Host.
 */
# $settings['reverse_proxy_host_header'] = 'X_FORWARDED_HOST';

/**
 * Set this value if your proxy server sends the client protocol in a header
 * other than X-Forwarded-Port.
 */
# $settings['reverse_proxy_port_header'] = 'X_FORWARDED_PORT';

/**
 * Set this value if your proxy server sends the client protocol in a header
 * other than Forwarded.
 */
# $settings['reverse_proxy_forwarded_header'] = 'FORWARDED';

/**
 * Page caching:
 *
 * By default, Drupal sends a "Vary: Cookie" HTTP header for anonymous page
 * views. This tells a HTTP proxy that it may return a page from its local
 * cache without contacting the web server, if the user sends the same Cookie
 * header as the user who originally requested the cached page. Without "Vary:
 * Cookie", authenticated users would also be served the anonymous page from
 * the cache. If the site has mostly anonymous users except a few known
 * editors/administrators, the Vary header can be omitted. This allows for
 * better caching in HTTP proxies (including reverse proxies), i.e. even if
 * clients send different cookies, they still get content served from the cache.
 * However, authenticated users should access the site directly (i.e. not use an
 * HTTP proxy, and bypass the reverse proxy if one is used) in order to avoid
 * getting cached pages from the proxy.
 */
# $settings['omit_vary_cookie'] = TRUE;


/**
 * Cache TTL for client error (4xx) responses.
 *
 * Items cached per-URL tend to result in a large number of cache items, and
 * this can be problematic on 404 pages which by their nature are unbounded. A
 * fixed TTL can be set for these items, defaulting to one hour, so that cache
 * backends which do not support LRU can purge older entries. To disable caching
 * of client error responses set the value to 0. Currently applies only to
 * page_cache module.
 */
# $settings['cache_ttl_4xx'] = 3600;


/**
 * Class Loader.
 *
 * If the APC extension is detected, the Symfony APC class loader is used for
 * performance reasons. Detection can be prevented by setting
 * class_loader_auto_detect to false, as in the example below.
 */
# $settings['class_loader_auto_detect'] = FALSE;

/*
 * If the APC extension is not detected, either because APC is missing or
 * because auto-detection has been disabled, auto-loading falls back to
 * Composer's ClassLoader, which is good for development as it does not break
 * when code is moved in the file system. You can also decorate the base class
 * loader with another cached solution than the Symfony APC class loader, as
 * all production sites should have a cached class loader of some sort enabled.
 *
 * To do so, you may decorate and replace the local $class_loader variable. For
 * example, to use Symfony's APC class loader without automatic detection,
 * uncomment the code below.
 */
/*
if ($settings['hash_salt']) {
  $prefix = 'drupal.' . hash('sha256', 'drupal.' . $settings['hash_salt']);
  $apc_loader = new \Symfony\Component\ClassLoader\ApcClassLoader($prefix, $class_loader);
  unset($prefix);
  $class_loader->unregister();
  $apc_loader->register();
  $class_loader = $apc_loader;
}
*/

/**
 * Authorized file system operations:
 *
 * The Update Manager module included with Drupal provides a mechanism for
 * site administrators to securely install missing updates for the site
 * directly through the web user interface. On securely-configured servers,
 * the Update manager will require the administrator to provide SSH or FTP
 * credentials before allowing the installation to proceed; this allows the
 * site to update the new files as the user who owns all the Drupal files,
 * instead of as the user the webserver is running as. On servers where the
 * webserver user is itself the owner of the Drupal files, the administrator
 * will not be prompted for SSH or FTP credentials (note that these server
 * setups are common on shared hosting, but are inherently insecure).
 *
 * Some sites might wish to disable the above functionality, and only update
 * the code directly via SSH or FTP themselves. This setting completely
 * disables all functionality related to these authorized file operations.
 *
 * @see https://www.drupal.org/node/244924
 *
 * Remove the leading hash signs to disable.
 */
# $settings['allow_authorize_operations'] = FALSE;

/**
 * Default mode for directories and files written by Drupal.
 *
 * Value should be in PHP Octal Notation, with leading zero.
 */
# $settings['file_chmod_directory'] = 0775;
# $settings['file_chmod_file'] = 0664;

/**
 * Public file base URL:
 *
 * An alternative base URL to be used for serving public files. This must
 * include any leading directory path.
 *
 * A different value from the domain used by Drupal to be used for accessing
 * public files. This can be used for a simple CDN integration, or to improve
 * security by serving user-uploaded files from a different domain or subdomain
 * pointing to the same server. Do not include a trailing slash.
 */
# $settings['file_public_base_url'] = 'http://downloads.example.com/files';

/**
 * Public file path:
 *
 * A local file system path where public files will be stored. This directory
 * must exist and be writable by Drupal. This directory must be relative to
 * the Drupal installation directory and be accessible over the web.
 */
$settings['file_public_path'] = 'sites/default/files';

/**
 * Private file path:
 *
 * A local file system path where private files will be stored. This directory
 * must be absolute, outside of the Drupal installation directory and not
 * accessible over the web.
 *
 * Note: Caches need to be cleared when this value is changed to make the
 * private:// stream wrapper available to the system.
 *
 * See https://www.drupal.org/documentation/modules/file for more information
 * about securing private files.
 */
$settings['file_private_path'] = realpath($app_root. '/../private');

/**
 * Session write interval:
 *
 * Set the minimum interval between each session write to database.
 * For performance reasons it defaults to 180.
 */
# $settings['session_write_interval'] = 180;

/**
 * String overrides:
 *
 * To override specific strings on your site with or without enabling the Locale
 * module, add an entry to this list. This functionality allows you to change
 * a small number of your site's default English language interface strings.
 *
 * Remove the leading hash signs to enable.
 *
 * The "en" part of the variable name, is dynamic and can be any langcode of
 * any added language. (eg locale_custom_strings_de for german).
 */
# $settings['locale_custom_strings_en'][''] = array(
#   'forum'      => 'Discussion board',
#   '@count min' => '@count minutes',
# );

/**
 * A custom theme for the offline page:
 *
 * This applies when the site is explicitly set to maintenance mode through the
 * administration page or when the database is inactive due to an error.
 * The template file should also be copied into the theme. It is located inside
 * 'core/modules/system/templates/maintenance-page.html.twig'.
 *
 * Note: This setting does not apply to installation and update pages.
 */
# $settings['maintenance_theme'] = 'bartik';

/**
 * PHP settings:
 *
 * To see what PHP settings are possible, including whether they can be set at
 * runtime (by using ini_set()), read the PHP documentation:
 * http://php.net/manual/ini.list.php
 * See \Drupal\Core\DrupalKernel::bootEnvironment() for required runtime
 * settings and the .htaccess file for non-runtime settings.
 * Settings defined there should not be duplicated here so as to avoid conflict
 * issues.
 */

/**
 * If you encounter a situation where users post a large amount of text, and
 * the result is stripped out upon viewing but can still be edited, Drupal's
 * output filter may not have sufficient memory to process it.  If you
 * experience this issue, you may wish to uncomment the following two lines
 * and increase the limits of these variables.  For more information, see
 * http://php.net/manual/pcre.configuration.php.
 */
# ini_set('pcre.backtrack_limit', 200000);
# ini_set('pcre.recursion_limit', 200000);

/**
 * Active configuration settings.
 *
 * By default, the active configuration is stored in the database in the
 * {config} table. To use a different storage mechanism for the active
 * configuration, do the following prior to installing:
 * - Create an "active" directory and declare its path in $config_directories
 *   as explained under the 'Location of the site configuration files' section
 *   above in this file. To enhance security, you can declare a path that is
 *   outside your document root.
 * - Override the 'bootstrap_config_storage' setting here. It must be set to a
 *   callable that returns an object that implements
 *   \Drupal\Core\Config\StorageInterface.
 * - Override the service definition 'config.storage.active'. Put this
 *   override in a services.yml file in the same directory as settings.php
 *   (definitions in this file will override service definition defaults).
 */
# $settings['bootstrap_config_storage'] = array('Drupal\Core\Config\BootstrapConfigStorageFactory', 'getFileStorage');

/**
 * Configuration overrides.
 *
 * To globally override specific configuration values for this site,
 * set them here. You usually don't need to use this feature. This is
 * useful in a configuration file for a vhost or directory, rather than
 * the default settings.php.
 *
 * Note that any values you provide in these variable overrides will not be
 * viewable from the Drupal administration interface. The administration
 * interface displays the values stored in configuration so that you can stage
 * changes to other environments that don't have the overrides.
 *
 * There are particular configuration values that are risky to override. For
 * example, overriding the list of installed modules in 'core.extension' is not
 * supported as module install or uninstall has not occurred. Other examples
 * include field storage configuration, because it has effects on database
 * structure, and 'core.menu.static_menu_link_overrides' since this is cached in
 * a way that is not config override aware. Also, note that changing
 * configuration values in settings.php will not fire any of the configuration
 * change events.
 */
# $config['system.site']['name'] = 'My Drupal site';
# $config['system.theme']['default'] = 'stark';
# $config['user.settings']['anonymous'] = 'Visitor';

/**
 * Fast 404 pages:
 *
 * Drupal can generate fully themed 404 pages. However, some of these responses
 * are for images or other resource files that are not displayed to the user.
 * This can waste bandwidth, and also generate server load.
 *
 * The options below return a simple, fast 404 page for URLs matching a
 * specific pattern:
 * - $config['system.performance']['fast_404']['exclude_paths']: A regular
 *   expression to match paths to exclude, such as images generated by image
 *   styles, or dynamically-resized images. The default pattern provided below
 *   also excludes the private file system. If you need to add more paths, you
 *   can add '|path' to the expression.
 * - $config['system.performance']['fast_404']['paths']: A regular expression to
 *   match paths that should return a simple 404 page, rather than the fully
 *   themed 404 page. If you don't have any aliases ending in htm or html you
 *   can add '|s?html?' to the expression.
 * - $config['system.performance']['fast_404']['html']: The html to return for
 *   simple 404 pages.
 *
 * Remove the leading hash signs if you would like to alter this functionality.
 */
# $config['system.performance']['fast_404']['exclude_paths'] = '/\/(?:styles)|(?:system\/files)\//';
# $config['system.performance']['fast_404']['paths'] = '/\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i';
# $config['system.performance']['fast_404']['html'] = '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

/**
 * Override the default service container class.
 *
 * This is useful for example to trace the service container for performance
 * tracking purposes, for testing a service container with an error condition or
 * to test a service container that throws an exception.
 */
# $settings['container_base_class'] = '\Drupal\Core\DependencyInjection\Container';

/**
 * Override the default yaml parser class.
 *
 * Provide a fully qualified class name here if you would like to provide an
 * alternate implementation YAML parser. The class must implement the
 * \Drupal\Component\Serialization\SerializationInterface interface.
 */
# $settings['yaml_parser_class'] = NULL;

/**
 * Trusted host configuration.
 *
 * Drupal core can use the Symfony trusted host mechanism to prevent HTTP Host
 * header spoofing.
 *
 * To enable the trusted host mechanism, you enable your allowable hosts
 * in $settings['trusted_host_patterns']. This should be an array of regular
 * expression patterns, without delimiters, representing the hosts you would
 * like to allow.
 *
 * For example:
 * @code
 * $settings['trusted_host_patterns'] = array(
 *   '^www\.example\.com$',
 * );
 * @endcode
 * will allow the site to only run from www.example.com.
 *
 * If you are running multisite, or if you are running your site from
 * different domain names (eg, you don't redirect http://www.example.com to
 * http://example.com), you should specify all of the host patterns that are
 * allowed by your site.
 *
 * For example:
 * @code
 * $settings['trusted_host_patterns'] = array(
 *   '^example\.com$',
 *   '^.+\.example\.com$',
 *   '^example\.org$',
 *   '^.+\.example\.org$',
 * );
 * @endcode
 * will allow the site to run off of all variants of example.com and
 * example.org, with all subdomains included.
 */

$appEnv = getenv('APP_ENV');

$settings['trusted_host_patterns'] = [
  '^par-beta-' . $appEnv . '\.cloudapps\.digital',
  $appEnv . '-cdn.par-beta.co.uk',
];

/**
 * The default list of directories that will be ignored by Drupal's file API.
 *
 * By default ignore node_modules and bower_components folders to avoid issues
 * with common frontend tools and recursive scanning of directories looking for
 * extensions.
 *
 * @see file_scan_directory()
 * @see \Drupal\Core\Extension\ExtensionDiscovery::scanDirectory()
 */
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];

/**
 * Ensure that config changes cannot be made through the UI.
 *
 * Restricts users from making changes that would be reset
 * if another deployment were made without exporting the values
 * to their config yaml files.
 */
$settings['config_readonly'] = TRUE;

/**
 * Extract the database credentials from the VCAP_SERVICES environment variable
 * which is configured by the PaaS service manager
 */
if ($env_services = getenv("VCAP_SERVICES")) {
  $services = json_decode($env_services);
  $credentials = isset($services->postgres) ? $services->postgres[0]->credentials : NULL;
}
if ($credentials) {
  $databases['default']['default'] = array (
    'database' => $credentials->name,
    'username' => $credentials->username,
    'password' => $credentials->password,
    'prefix' => '',
    'host' => $credentials->host,
    'port' => $credentials->port,
    'namespace' => 'Drupal\\Core\\Database\\Driver\\pgsql',
    'driver' => 'pgsql',
  );
}

// Set flysystem configuration to use local files for all environments,
// and S3 buckets for production files. We also have an artifact bucket
// for database backups and test reports.
$settings['flysystem'] = [
  's3public' => [
    'name' => 'S3 Public',
    'description' => 'The S3 store for public files.',
    'driver' => 'local',
    'config' => [
      'root' => $settings['file_public_path'],
      'public' => TRUE,
    ],
    'cache' => TRUE,
    'serve_js' => TRUE,
    'serve_css' => TRUE,
  ],
  's3private' => [
    'name' => 'S3 Private',
    'description' => 'The S3 store for private files.',
    'driver' => 'local',
    'config' => [
      'root' => $settings['file_private_path'],
    ],
    'cache' => TRUE,
    'serve_js' => FALSE,
    'serve_css' => FALSE,
  ],
];

// Only use S3 public store when required.
if (getenv('S3_BUCKET_ARTIFACTS')) {
  $settings['flysystem']['s3backups'] = [
    'name' => 'S3 Database Backups',
    'description' => 'The S3 store for database backups.',
    'driver' => 's3',
    'config' => [
      'key'    => getenv('S3_ACCESS_KEY'),
      'secret' => getenv('S3_SECRET_KEY'),
      'region' => 'eu-west-1',
      'bucket' => getenv('S3_BUCKET_ARTIFACTS'),
      'prefix' => 'backups',
    ],
    'cache' => TRUE,
    'serve_js' => FALSE,
    'serve_css' => FALSE,
  ];
}

// Only use S3 public store when required.
if (getenv('S3_BUCKET_PUBLIC')) {
  $settings['flysystem']['s3public'] = [
    'driver' => 's3',
    'config' => [
      'key'    => getenv('S3_ACCESS_KEY'),
      'secret' => getenv('S3_SECRET_KEY'),
      'region' => 'eu-west-1',
      'bucket' => getenv('S3_BUCKET_PUBLIC'),
      'prefix' => getenv('APP_ENV'),
    ],
  ] + $settings['flysystem']['s3public'];
}

// Only use S3 private store when required.
if (getenv('S3_BUCKET_PRIVATE')) {
  $settings['flysystem']['s3private'] = [
    'driver' => 's3',
    'config' => [
      'key'    => getenv('S3_ACCESS_KEY'),
      'secret' => getenv('S3_SECRET_KEY'),
      'region' => 'eu-west-1',
      'bucket' => getenv('S3_BUCKET_PRIVATE'),
      'prefix' => getenv('APP_ENV'),
    ],
  ] + $settings['flysystem']['s3private'];
}

/**
 * Set GovUK Notify settings.
 *
 * These are confidential and should be set with ENV variables.
 *
 * SPECIAL ATTENTION MUST BE MADE TO ENSURE THE PRODUCTION KEY
 * CAN NEVER BE SET ON ANYTHING BUT PRODUCTION ENVIRONMENT.
 *
 * Consider using a backup method for blocking emails on test envs.
 * @see https://www.drupal.org/docs/develop/local-server-setup/managing-mail-handling-for-development-or-testing
 */
$config['govuk_notify.settings']['api_key'] = getenv('PAR_GOVUK_NOTIFY_KEY');
$config['govuk_notify.settings']['default_template_id'] = getenv('PAR_GOVUK_NOTIFY_TEMPLATE');

/**
 * Set the Raven Sentry keys.
 *
 * These are confidential and should be set with ENV variables.
 *
 * All error and exception logs are sent to Sentry for reporting.
 */
$config['raven.settings']['client_key'] = getenv('SENTRY_DSN');
$config['raven.settings']['public_dsn'] = getenv('SENTRY_DSN_PUBLIC');

// Ensure all environments use production config unless overwritten.
$config['config_split.config_split.dev_config']['status'] = FALSE;
$config['config_split.config_split.beta_config']['status'] = FALSE;

/**
 * Environment settings override.
 *
 * Load specific settings for each app environment.
 */
if (!empty($appEnv) && file_exists("{$app_root}/{$site_path}/settings.local.{$appEnv}.php")) {
  include "{$app_root}/{$site_path}/settings.local.{$appEnv}.php";
}

/**
 * Load local development override configuration, if available.
 *
 * Use settings.local.php to override variables on secondary (staging,
 * development, etc) installations of this site. Typically used to disable
 * caching, JavaScript/CSS compression, re-routing of outgoing emails, and
 * other things that should not happen on development and testing sites.
 *
 * Keep this code block at the end of this file to take full effect.
 */
if (file_exists("{$app_root}/{$site_path}/settings.local.php")) {
  include "{$app_root}/{$site_path}/settings.local.php";
}

