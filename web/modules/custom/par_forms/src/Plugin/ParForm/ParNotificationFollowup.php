<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
  public function getElements($form = [], $cardinality = 1) {
    $form['notifications'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('No notifications will be sent'),
      '#description' => $this->t('Please confirm that you will follow up this matter with the affected parties.'),
      '#return_value' => self::FOLLOWUP_CONFIRM,
      '#attributes' => ['class' => ['form-group']],
    ];

    return $form;
  }

  /**
   * Validate checkbox.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    if (!$form_state->getValue('notifications')) {
      $id = $this->getElementId('notifications', $form);
      $form_state->setErrorByName($this->getElementName(['notifications']), $this->wrapErrorMessage('Please confirm that you will follow up on this with the affected parties.', $id));
    }

    parent::validate($form, $form_state, $cardinality, $action);
  }
}
