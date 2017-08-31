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

  // Sanitise all PAR Data Person records.
  // We'll randomly select a different person each
  // time so that we don't rely on any of the values.
  $replacement_people = [
    [
      "first_name='Big'",
      "last_name='Bird'",
    ],
    [
      "first_name='Aloysius'",
      "last_name='Snuffleupagus'",
    ],
    [
      "first_name='Kermit'",
      "last_name='the Frog'",
    ],
    [
      "first_name='Elmo'",
      "last_name='Muppet'",
    ],
    [
      "first_name='Ernie'",
      "last_name='Muppet'",
    ],
    [
      "first_name='Bert'",
      "last_name='Muppet'",
    ],
    [
      "first_name='Oscar'",
      "last_name='the Grouch'",
    ],
    [
      "first_name='Cookie'",
      "last_name='Monster'",
    ],
    [
      "first_name='Count'",
      "last_name='von Count'",
    ],
    [
      "first_name='Animal'",
      "last_name='Muppet'",
    ],
    [
      "first_name='Groover'",
      "last_name='Muppet'",
    ],
  ];

  // Set the replacement fields.
  $replacements = $replacement_people[rand(0,count($replacement_people)-1)] + [
    "work_phone='00441234567890'",
    "mobile_phone='00449876543210'",
  ];

  $salt = Settings::getHashSalt();
  //crypt('$mail', '$salt')

  // Sanitize email addresses.
  $newemail = drush_get_option(['sanitize-email', 'destination-sanitize-email'], '%mailhash@localhost.localdomain');
  // Within PAR we have to use an email format that encrypts the
  // email itself so that encrypting in multiple locations maintains
  // the same encrypted value. This cannot be changed.
  $email_format = '%mailhash@localhost.localdomain';

  // We need a different sanitization query for Postgres and Mysql.
  $db_driver = $databases['default']['default']['driver'];
  if ($db_driver == 'pgsql') {
    $email_map = ['%mailhash' => "crypt(' || email || ', '{$salt}') "];
    $email_format =  str_replace(array_keys($email_map), array_values($email_map), $email_format);
    $inner = "SELECT a::text || ', ' || b::text AS ab FROM foo;";
  }
  $replacements[] = "email=$email_format";

  if (!empty($replacements) && $major_version >= 8) {
    $table = $wrap_table_name ? "{{par_people_field_data}}" : 'par_people_field_data';
    $revisions_table = $wrap_table_name ? '{{par_people_field_revision}}' : 'par_people_field_revision';

    // Generate the SQL query to replace.
    $sets = implode(', ', $replacements);
    $par_data_person_query = <<<SQL
UPDATE {$table} AS p
SET {$sets}
WHERE id > 0;
SQL;
    $par_data_person_revision_query = <<<SQL
UPDATE {$revisions_table} AS p
SET {$sets}
WHERE revision_id > 0;
SQL;

    var_dump($par_data_person_query);

    drush_sql_register_post_sync_op('sanitise_par_people', dt('Sanitise PAR Data Person records'), $par_data_person_query);
    drush_sql_register_post_sync_op('sanitise_par_people_revisions', dt('Sanitise PAR Data Person revision records'), $par_data_person_revision_query);
  }
}
