<?php

namespace Drupal\par_manage_subscription_list_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;

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

  public function getSubscriptionManager() {
    return \Drupal::service('par_subscriptions.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Determine which subscriptions need to be subscribed or unsubscribed.
    $cid = $this->getFlowNegotiator()->getFormKey('manage_subscriptions');
    $subscribe = $this->getFlowDataHandler()->getTempDataValue('subscribe', $cid);
    $unsubscribe = $this->getFlowDataHandler()->getTempDataValue('unsubscribe', $cid);

    if (!empty($subscribe)) {
      $subscribe_list = implode(', ', $subscribe);
      $form['subscribe'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Subscriptions to be added (@count)', ['@count' => count($subscribe)]),
        '#title_tag' => 'h2',
        '#attributes' => ['class' => 'govuk-form-group'],
        'new' => [
          '#type' => 'markup',
          '#markup' => "$subscribe_list",
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ]
      ];
    }
    if (!empty($unsubscribe)) {
      $unsubscribe_list = implode(', ', $unsubscribe);
      $form['unsubscribe'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Subscriptions to be removed (@count)', ['@count' => count($unsubscribe)]),
        '#attributes' => ['class' => 'govuk-form-group'],
        'new' => [
          '#type' => 'markup',
          '#markup' => "$unsubscribe_list",
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ]
      ];
    }

    // Change primary action.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Update list');

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

    // Determine which subscriptions need to be subscribed or unsubscribed.
    $cid = $this->getFlowNegotiator()->getFormKey('manage_subscriptions');
    $subscribe = $this->getFlowDataHandler()->getTempDataValue('subscribe', $cid) ?? [];
    $unsubscribe = $this->getFlowDataHandler()->getTempDataValue('unsubscribe', $cid) ?? [];

    $list = $this->getFlowDataHandler()->getTempDataValue('list', $cid);

    foreach ($subscribe as $email) {
      $subscription = $this->getSubscriptionManager()->createSubscription($list, $email);
      if ($subscription instanceof ParSubscriptionInterface) {
        $subscription->verify();
      }
    }
    foreach ($unsubscribe as $email) {
      $subscription = $this->getSubscriptionManager()->getSubscriptionByEmail($list, $email);
      if ($subscription instanceof ParSubscriptionInterface) {
        $subscription->unsubscribe();
      }
    }

    $this->getFlowDataHandler()->deleteStore();
  }

}
