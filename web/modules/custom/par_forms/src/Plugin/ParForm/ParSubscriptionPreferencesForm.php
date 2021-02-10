<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\Xss;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Subscription list form plugin.
 *
 * @ParForm(
 *   id = "subscription_preferences",
 *   title = @Translation("Subscription preferences form.")
 * )
 */
class ParSubscriptionPreferencesForm extends ParFormPluginBase {

  public function getSubscriptionManager() {
    return \Drupal::service('par_subscriptions.manager');
  }

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $lists = $this->getSubscriptionManager()->getLists();
    $account = $this->getFlowDataHandler()->getParameter('user');

    $subscription_preferences = [];
    foreach ($lists as $list) {
      if ($account && $subscription = $this->getSubscriptionManager()->getSubscriptionByEmail($list, $account->getEmail())) {
        $subscription_preferences[$list] = $subscription->getCode();
      }
    }

    $this->getFlowDataHandler()->setFormPermValue('subscription_lists', $lists);
    $this->getFlowDataHandler()->setFormPermValue('subscription_preferences', $subscription_preferences);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Skip this form if there are no lists.
    $subscription_lists = $this->getFlowDataHandler()->getFormPermValue('subscription_lists');
    if (count($subscription_lists) <= 0) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    $form['help'] = [
      '#markup' => '<p>You can choose to subscribe to our Mailing lists below</p>',
    ];

    $subscription_preferences = $this->getFlowDataHandler()->getFormPermValue('subscription_preferences');
    $form['subscriptions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Subscribe to the mailing list'),
      '#description' => '<p>\.</p>',
      '#options' => $subscription_lists,
      '#default_value' => $this->getDefaultValuesByKey('subscriptions', $cardinality, $subscription_preferences),
      '#return_value' => 'on',
    ];

    return $form;
  }
}
