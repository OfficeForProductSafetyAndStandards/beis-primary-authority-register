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

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Delete the partnership'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['partnership_info']['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => 'qweqwe',
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $methods = [
      self::METHOD_INSERT => 'Insert any new emails into the list, without removing any.',
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

    // Enter the deletion reason.
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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // The method used to update the subscription list: insert, remove or replace.
    $method = $this->getFlowDataHandler()->getTempDataValue('method');
  }

}
