<?php

use Drupal\Component\Utility\Random;
use Drupal\Core\Site\Settings;

/**
 * Implements hook_drush_sql_sync_sanitize.
 */
function par_data_drush_sql_sync_sanitize($site) {
  $site_settings = drush_sitealias_get_record($site);
  $databases = sitealias_get_databases_from_record($site_settings);
  $major_version = drush_drupal_major_version();
  $wrap_table_name = (bool) drush_get_option('db-prefix');

  // Our sanitization is only compatible with postgres.
  $db_driver = $databases['default']['default']['driver'];

  // Within PAR we have to use an email format that encrypts the
  // email itself so that encrypting in multiple locations maintains
  // the same level of uniqueness, this is not configurable in PAR.
  if ($db_driver == 'pgsql' && $major_version >= 8) {
    $table = $wrap_table_name ? "{{par_people_field_data}}" : 'par_people_field_data';
    $revisions_table = $wrap_table_name ? '{{par_people_field_revision}}' : 'par_people_field_revision';

    // Generate the SQL query to replace.
    $user_query = <<<SQL
UPDATE users_field_data AS u
SET init=sub.hashemail, mail=sub.hashemail, name=sub.name
FROM (SELECT trunc(random() * 999999999999 + 1) as name, mail, md5(mail) || '@localhost.localdomain' as hashemail
      FROM users_field_data) AS sub
WHERE u.mail=sub.mail
AND u.uid > 1 AND u.mail NOT LIKE '';
SQL;
    $par_data_person_query = <<<SQL
UPDATE {$table} AS p
SET work_phone=sub.work_phone, mobile_phone=sub.mobile_phone, first_name=sub.first_name, last_name=sub.last_name, email=sub.hashemail
FROM (SELECT id, trunc(random() * 99999999 + 1) as work_phone, trunc(random() * 99999999 + 1) as mobile_phone, trunc(random() * 99999 + 1) as first_name, trunc(random() * 99999999 + 1) as last_name, email, md5(email) || '@localhost.localdomain' as hashemail
      FROM {$table}) AS sub
WHERE p.id=sub.id;
SQL;
    $par_data_person_revision_query = <<<SQL
UPDATE {$revisions_table} AS p
SET work_phone=sub.work_phone, mobile_phone=sub.mobile_phone, first_name=sub.first_name, last_name=sub.last_name, email=sub.hashemail
FROM (SELECT id, trunc(random() * 99999999 + 1) as work_phone, trunc(random() * 99999999 + 1) as mobile_phone, trunc(random() * 99999 + 1) as first_name, trunc(random() * 99999999 + 1) as last_name, email, md5(email) || '@localhost.localdomain' as hashemail
      FROM {$revisions_table}) AS sub
WHERE p.id=sub.id;
SQL;

    drush_sql_register_post_sync_op('sanitise_drupal_users', dt('Sanitise Drupal Users the PAR way'), $user_query);
    drush_sql_register_post_sync_op('sanitise_par_people', dt('Sanitise PAR Data Person records'), $par_data_person_query);
    drush_sql_register_post_sync_op('sanitise_par_people_revisions', dt('Sanitise PAR Data Person revision records'), $par_data_person_revision_query);
  }
}

/**
 * Implements hook_drush_command().
 */
function par_data_drush_command() {
  $commands = [];
  $commands['par-cache-warm'] = [
    'description' => 'Warm the caches for PAR Data',
    'aliases' => ['pcw'],
    'examples' => [
      'drush pcw',
    ],
  ];
  return $commands;
}

function drush_par_data_par_cache_warm() {
  $par_data_manager = \Drupal::service('par_data.manager');

  $authorities = $par_data_manager->getEntitiesByType('par_data_authority');
  $i = 0;
  $authority_count = count($authorities);
  foreach ($authorities as $authority) {
    $authority->getRelationships();
    if ($i % 100 == 0 && $i > 0) {
      drush_print("Warmed $i out of $authority_count authorities");
    }
    $i++;
  }
  drush_print("Completed warming par authorities");

  $organisations = $par_data_manager->getEntitiesByType('par_data_organisation');
  $i=0;
  $organisation_count = count($organisations);
  foreach ($organisations as $organisation) {
    $organisation->getRelationships();
    if ($i % 100 == 0 && $i > 0) {
      drush_print("Warmed $i out of $organisation_count organisations");
    }
    $i++;
  }
  drush_print("Completed warming par organisations");

  return 'Cache warmed for authorities and organisations.';
}
