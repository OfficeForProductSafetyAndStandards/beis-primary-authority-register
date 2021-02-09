<?php

namespace Drupal\par_subscriptions\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\par_subscriptions\ParSubscriptionManager;
use Drupal\par_subscriptions\ParSubscriptionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form controller for subscription lists.
 */
class ParVerifyForm extends FormBase  {

  /**
   * Constructs a subscription controller for rendering requests.
   */
  public function __construct(ParSubscriptionManagerInterface $par_subscriptions_manager) {
    $this->subscriptionManager = $par_subscriptions_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('par_subscriptions.manager'));
  }

  /**
   * Dynamic getter for the messenger service.
   *
   * @return \Drupal\par_subscriptions\ParSubscriptionManagerInterface
   */
  private function getSubscriptionManager() {
    return $this->subscriptionManager ?? \Drupal::service('par_subscriptions.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'subscription_list_verify';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback($list = NULL) {
    return "Verify subscription to {$this->getSubscriptionManager()->getListName($list)}";
  }

  /**
   * Verify a subscription to a list.
   *
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $list = NULL, $subscription_code = NULL) {
    $subscription = $this->getSubscriptionManager()->getSubscription($subscription_code);
    if ($subscription) {
      // Verify the subscription.
      $subscription->verify();

      $form['verified'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Thank you, your subscription has been activated.'),
        '#attributes' => ['class' => 'subscription-help'],
      ];
    }
    else {
      $form['subscribe'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Your subscription cannot be verified at this time, please try re-sending your verification link.'),
        '#attributes' => ['class' => 'subscription-help'],
      ];

      $route_name = "par_subscriptions.{$list}.subscribe";
      $form['link'] = [
        '#title' => $this->t('Re-send verification'),
        '#type' => 'link',
        '#url' => Url::fromRoute($route_name),
      ];
    }

    $link_name = $this->currentUser()->isAuthenticated() ? 'Go back to dashboard' : 'Go to the home page';
    $route = $this->currentUser()->isAuthenticated() ? 'par_dashboards.dashboard' : '<front>';
    $form['back'] = [
      '#title' => $this->t($link_name),
      '#type' => 'link',
      '#url' => Url::fromRoute($route),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
