<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Notification followup form plugin.
 *
 * @ParForm(
 *   id = "notification_followup",
 *   title = @Translation("Let the user know no notifications will be sent.")
 * )
 */
class ParNotificationFollowup extends ParFormPluginBase {

  /**
   * The value for the policy confirmation.
   */
  const FOLLOWUP_CONFIRM = 'confirmed';

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $form['notifications'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => ['class' => ['form-group']],
      '#value' => $this->t('No notifications will be sent, please follow up this matter with the affected parties.'),
    ];

    return $form;
  }
}
