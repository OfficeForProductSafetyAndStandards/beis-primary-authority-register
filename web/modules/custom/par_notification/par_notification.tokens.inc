<?php

/**
 * @file
 * Builds placeholder replacement tokens for message-related data.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info_alter().
 */
function par_notification_token_info_alter(&$data) {
  if (isset($data['tokens']['message'])) {
    // Add a new message token.
    $data['tokens']['message']['primary-action'] = array(
      'name' => t("Primary action"),
      'description' => t("The primary action link for this message."),
    );
  }
}

/**
 * Implements hook_tokens().
 */
function par_notification_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $link_manager = \Drupal::service('plugin.manager.par_link_manager');

  $replacements = [];

  if ($type == 'message' && !empty($data['message'])) {
    /** @var \Drupal\message\Entity\Message $message */
    $message = $data['message'];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        // Simple key values on the message.
        case 'primary-action':
          $replacements[$original] = $link_manager->generateLink($message->id())->toString();
          break;
      }
    }
  }

  return $replacements;
}
