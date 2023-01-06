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
  $data['message']['access'] = [
    'title' => t('Access'),
    'help' => t('Add access control the the notification.'),
    'filter' => [
      'id' => 'view_messages',
    ],
  ];
  $data['message']['tasks'] = [
    'title' => t('Action required'),
    'help' => t('Whether the notification requires action'),
    'field' => [
      'id' => 'par_notification_action',
    ],
    'filter' => [
      'id' => 'task_messages',
    ],
  ];
  // Add a field to add the primary action link.
  $data['message']['notification_link'] = [
    'title' => t('Notification link'),
    'help' => t('The primary action link for the notification'),
    'field' => [
      'id' => 'par_notification_link',
    ],
  ];
  // Add a field to display the primary entity label for the message.
  $data['message']['notification_for'] = [
    'title' => t('Notification for'),
    'help' => t('The label for the primary entity associated with the notification'),
    'field' => [
      'id' => 'par_notification_for',
    ],
  ];
}
