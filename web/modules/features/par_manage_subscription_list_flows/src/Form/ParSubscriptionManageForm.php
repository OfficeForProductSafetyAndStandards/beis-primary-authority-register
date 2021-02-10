<?php

namespace Drupal\par_manage_subscription_list_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Make changes to a subscription list.
 */
class ParSubscriptionManageForm extends ParBaseForm {

  const METHOD_INSERT = 'insert';
  const METHOD_REMOVE = 'remove';
  const METHOD_REPLACE = 'replace';

  /**
   * {@inheritdoc}
   */
  protected $flow = 'manage_subscriptions';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Help Desk | Manage a subscription list';
  }

  public function getEmailValidator() {
    return \Drupal::service('email.validator');
  }

  public function getSubscriptionManager() {
    return \Drupal::service('par_subscriptions.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $list = NULL) {
    $methods = [
      self::METHOD_INSERT => 'Insert new emails into the list, without removing any.',
      self::METHOD_REMOVE => 'Remove these emails from the list, without adding any new subscriptions.',
      self::METHOD_REPLACE => 'Replace the old subscription list with this new one.',
    ];
    $form['method'] = [
      '#type' => 'radios',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => $this->t('Update method'),
      '#options' => $methods,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('method', self::METHOD_INSERT),
    ];

    $form['list'] = [
      '#type' => 'hidden',
      '#value' => $list,
    ];

    $form['emails'] = [
      '#title' => $this->t('Enter the email addresses, each one on a new line.'),
      '#type' => 'textarea',
      '#rows' => 15,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('emails', FALSE),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Define array variable.
    $rows = [];

    // Process uploaded csv file.
    if ($raw = $this->getFlowDataHandler()->getTempDataValue('emails')) {
      $emails = explode(PHP_EOL, $raw);
      $chars = " ,.\n\r\t\v\0";

      // Validate each row.
      foreach ($emails as $i => $email) {
        $clean = trim($email, $chars);
        if ($this->getEmailValidator()->isValid($clean)) {
          $rows[$i] = $clean;
        }
      }

      // Calculate errors by comparing validated rows.
      $errors = array_diff_key($emails, $rows);
      if (!empty($errors)) {
        $id = $this->getElementId(['emails'], $form);
        $message = count($errors) <= 5 ?
          "Errors were detected with these emails: " . implode(', ', array_values($errors)) :
          "Errors were detected on the following lines: " . implode(', ', array_keys($errors));
        $form_state->setErrorByName($this->getElementName('emails'), $this->wrapErrorMessage($message, $id));
      }
    }

    if (count($rows) > 0) {
      $form_state->setValue('subscribers', $rows);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $list = $this->getFlowDataHandler()->getTempDataValue('list');
    $method = $this->getFlowDataHandler()->getTempDataValue('method');

    $current = $this->getSubscriptionManager()->getListEmails($list);
    $new = $this->getFlowDataHandler()->getTempDataValue('subscribers');

    switch ($method) {
      case self::METHOD_INSERT:
        $subscribe = array_diff($new, $current);

        break;

      case self::METHOD_REMOVE:
        $unsubscribe = array_intersect($new, $current);

        break;

      case self::METHOD_REPLACE:
        $unsubscribe = array_diff($current, $new);
        $subscribe = array_diff($new, $current);

    }

    $this->getFlowDataHandler()->setTempDataValue('subscribe', $subscribe ?? []);
    $this->getFlowDataHandler()->setTempDataValue('unsubscribe', $unsubscribe ?? []);
  }

}
