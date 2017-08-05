<?php

use Drupal\Component\Utility\Random;

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

  // Sanitize email addresses.
  $newemail = drush_get_option(array('sanitize-email', 'destination-sanitize-email'), 'user+%uid@localhost.localdomain');
  if ($newemail != 'no' && $newemail !== 0) {
    if (strpos($newemail, '%') !== FALSE) {
      // We need a different sanitization query for Postgres and Mysql.
      $db_driver = $databases['default']['default']['driver'];
      if ($db_driver == 'pgsql') {
        $email_map = array('%uid' => "' || user_account || '", '%mail' => "' || replace(mail, '@', '_') || '", '%name' => "' || replace(first_name, ' ', '_') || '");
        $newmail =  "'" . str_replace(array_keys($email_map), array_values($email_map), $newemail) . "'";
      }
      elseif ($db_driver == 'mssql') {
        $email_map = array('%uid' => "' + user_account + '", '%mail' => "' + replace(mail, '@', '_') + '", '%name' => "' + replace(first_name, ' ', '_') + '");
        $newmail =  "'" . str_replace(array_keys($email_map), array_values($email_map), $newemail) . "'";
      }
      else {
        $email_map = array('%uid' => "', user_account, '", '%mail' => "', replace(mail, '@', '_'), '", '%name' => "', replace(first_name, ' ', '_'), '");
        $newmail =  "concat('" . str_replace(array_keys($email_map), array_values($email_map), $newemail) . "')";
      }
      $replacements[] = "email=$newmail";
    }
    else {
      $replacements[] = "email='$newemail'";
    }
  }

  if (!empty($replacements) && $major_version >= 8) {
    $table = $wrap_table_name ? "{{par_people_field_data}}" : 'par_people_field_data';

    // Generate the SQL query to replace
    $sets = implode(', ', $replacements);
    $par_data_person_query = <<<SQL
UPDATE {$table}
SET {$sets}
WHERE id > 0;
SQL;

    drush_sql_register_post_sync_op('sanitise_par_people', dt('Sanitise PAR Data Person records'), $par_data_person_query);
  }
}
