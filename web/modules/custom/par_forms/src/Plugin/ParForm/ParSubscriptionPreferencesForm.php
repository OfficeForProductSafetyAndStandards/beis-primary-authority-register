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

    $subscription_lists = [];
    $subscription_preferences = [];
    foreach ($lists as $list) {
      $subscription_lists[$list] = $this->getSubscriptionManager()->getListName($list);
      if ($account && $subscription = $this->getSubscriptionManager()->getSubscriptionByEmail($list, $account->getEmail())) {
        $subscription_preferences[$list] = $subscription->getCode();
      }
    }

    $this->getFlowDataHandler()->setFormPermValue('subscription_lists', $subscription_lists);
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

    $subscription_preferences = $this->getFlowDataHandler()->getFormPermValue('subscription_preferences');
    $form['subscriptions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Subscribe to the mailing list'),
      '#description' => '<p>All out news letters are sent round no more than once a month, you can unsubscribe at any time.</p>',
      '#options' => $subscription_lists,
      '#attributes' => ['class' => ['form-group']],
      '#default_value' => $this->getDefaultValuesByKey('subscriptions', $cardinality, array_keys($subscription_preferences)),
      '#return_value' => 'on',
    ];

    return $form;
  }
}
