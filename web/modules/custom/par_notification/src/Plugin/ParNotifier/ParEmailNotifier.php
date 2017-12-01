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

  public function getLanguageManager() {
    return \Drupal::languageManager();
  }

  public function deliver($recipient, $message) {
    $mailer = \Drupal::service('plugin.manager.mail');

    // Not all users will have accounts.
    if ($account = $recipient->getUserAccount()){
      $language = $recipient->getUserAccount()->getPreferredLangcode();
    }
    else {
      $language = $this->getLanguageManager()->getCurrentLanguage()->getId();
    }

    $params = [
      'subject' => $message->getSubject(),
      'body' => [$message->getMessage()],
    ];

    $sent = $mailer->mail('par_notification', $message->id(), $recipient->getEmail(), $language, $params);

    if ($sent) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}
