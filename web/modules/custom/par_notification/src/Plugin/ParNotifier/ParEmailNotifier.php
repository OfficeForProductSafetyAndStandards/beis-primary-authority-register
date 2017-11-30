<?php

namespace Drupal\par_notification\Plugin\ParNotifier;

/**
 * Approve an enforcement notice.
 *
 * @ParNotifier(
 *   id = "email",
 *   label = @Translation("Sends notifications by e-mail address.")
 * )
 */
class ParEmailNotifier implements ParNotifierPluginInterface {

  public function deliver($recipient, $message) {
    $mailer = \Drupal::service('plugin.manager.mail');

    $params = [
      'subject' => $message->getSubject(),
      'body' => [$message->getMessage()],
    ];
    $sent = $mailer->mail('par_notification', $message->id(), $recipient->getEmail(), $recipient->getPreferredLangcode(), $params);

    if ($sent) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}
