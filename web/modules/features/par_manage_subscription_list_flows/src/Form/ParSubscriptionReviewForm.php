<?php

namespace Drupal\par_manage_subscription_list_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Review changes to a subscription list.
 */
class ParSubscriptionReviewForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'review_subscriptions';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Help Desk | Review changes';
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
