<?php

/**
 * @file
 * The views data file.
 */

use \Drupal\search_api\Entity\Index;
use \Drupal\Core\Field\TypedData\FieldItemDataDefinition;
use \Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\message\MessageInterface;

/**
 * Implements hook_views_data_alter().
 */
function par_notification_views_data_alter(array &$data) {
  // Check that message has been installed.
  $moduleHandler = \Drupal::service('module_handler');
  if (!isset($data['message']) || !$moduleHandler->moduleExists('message')) {
    return;
  }

  // Add a field to process whether action is required.
//  $data['message']['action'] = [
//    'title' => t('Action required'),
//    'help' => t('Whether the notification requires action'),
//    'field' => [
//      'id' => 'par_notification_action',
//    ],
//  ];
//  // Add a field to add the primary action link.
//  $data['message']['link'] = [
//    'title' => t('Notification link'),
//    'help' => t('The primary action link for the notification'),
//    'field' => [
//      'id' => 'par_notification_link',
//    ],
//  ];
}
