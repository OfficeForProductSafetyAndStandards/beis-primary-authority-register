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

//  $par_data_organisation = $par_data_manager->getEntitiesByType('par_data_organisation');
//  $par_data_authority = $par_data_manager->getEntitiesByType('par_data_authority');
//  $par_data_coordinated_business = $par_data_manager->getEntitiesByType('par_data_coordinated_business');
//  $par_data_advice = $par_data_manager->getEntitiesByType('par_data_advice');
//  $par_data_inspection_plan = $par_data_manager->getEntitiesByType('par_data_inspection_plan');
//  $par_data_inspection_feedback = $par_data_manager->getEntitiesByType('par_data_inspection_feedback');
//  $par_data_general_enquiry = $par_data_manager->getEntitiesByType('par_data_general_enquiry');
//  $par_data_deviation_request = $par_data_manager->getEntitiesByType('par_data_deviation_request');
//  $par_data_information_referral = $par_data_manager->getEntitiesByType('par_data_information_referral');
//  $par_data_legal_entity = $par_data_manager->getEntitiesByType('par_data_legal_entity');
//  $par_data_enforcement_notice = $par_data_manager->getEntitiesByType('par_data_enforcement_notice');
//  $par_data_enforcement_action = $par_data_manager->getEntitiesByType('par_data_enforcement_action');
//  $par_data_sic_code = $par_data_manager->getEntitiesByType('par_data_sic_code');
//  $par_data_person = $par_data_manager->getEntitiesByType('par_data_person');
//  $par_data_premises = $par_data_manager->getEntitiesByType('par_data_premises');
//  $par_data_partnership = $par_data_manager->getEntitiesByType('par_data_partnership');
//  $par_data_regulatory_function = $par_data_manager->getEntitiesByType('par_data_regulatory_function');
//
  $count = 0;

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_organisation') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_authority') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_coordinated_business') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_advice') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_inspection_plan') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_inspection_feedback') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_general_enquiry') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_deviation_request') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_information_referral') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_legal_entity') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_enforcement_notice') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_enforcement_action') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_person') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_premises') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;
  foreach ($par_data_manager->getEntitiesByType('par_data_partnership') as $entity) {
    $count++;
    $entity->getRelationships();
  }

  echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB before iteration ' . $i . PHP_EOL;

  return "Cache warmed for all $count data entities.";
}
