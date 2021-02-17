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

    // Process email addresses.
    if ($raw = $this->getFlowDataHandler()->getTempDataValue('emails')) {
      $emails = explode(PHP_EOL, $raw);
      $chars = " ,.\n\r\t\v\0";

      // Validate each row.
      foreach ($emails as $i => &$email) {
        $email = trim($email, $chars);
        if ($this->getEmailValidator()->isValid($email)) {
          $rows[$i] = $email;
        }
      }

      // Remove any empty rows.
      $emails = array_filter($emails);

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

    if (count($rows) <= 0) {
      $id = $this->getElementId(['emails'], $form);
      $message = "No valid email addresses have been submitted.";
      $form_state->setErrorByName($this->getElementName('emails'), $this->wrapErrorMessage($message, $id));
    }

    // Determine which subscribers are new and which are existing.
    $list = $this->getFlowDataHandler()->getTempDataValue('list');
    $method = $this->getFlowDataHandler()->getTempDataValue('method');

    $current = $this->getSubscriptionManager()->getListEmails($list);
    $new = $rows;

    switch ($method) {
      case self::METHOD_INSERT:
        $subscribe = array_diff($new, $current);
        $error_message = "There are no new subscribers to add.";

        break;

      case self::METHOD_REMOVE:
        $unsubscribe = array_intersect($new, $current);
        $error_message = "There are no existing subscribers matching these addresses.";

        break;

      case self::METHOD_REPLACE:
        $unsubscribe = array_diff($current, $new);
        $subscribe = array_diff($new, $current);
        $error_message = "There are no changes to be made.";

    }

    // If there are no changes to make show an error.
    if (empty($subscribe) && empty($unsubscribe) && $error_message) {
      $id = $this->getElementId(['emails'], $form);
      $form_state->setErrorByName($this->getElementName('emails'), $this->wrapErrorMessage($error_message, $id));
    }

    $form_state->setValue('subscribe', $subscribe ?? []);
    $form_state->setValue('unsubscribe', $unsubscribe ?? []);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
