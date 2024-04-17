<?php

namespace Drupal\par_subscriptions\Form;

use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;
use Drupal\par_subscriptions\ParSubscriptionManager;
use Drupal\par_subscriptions\ParSubscriptionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form controller for subscription lists.
 */
class ParSubscribeForm extends FormBase {

  /**
   * The flood service.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * Constructs a subscription controller for rendering requests.
   *
   * @param \Drupal\par_subscriptions\Entity\ParSubscription
   *   The subscription manager.
   * @param \Drupal\Core\Flood\FloodInterface $flood
   *   The flood service.
   */
  public function __construct(ParSubscriptionManagerInterface $par_subscriptions_manager, FloodInterface $flood) {
    $this->subscriptionManager = $par_subscriptions_manager;
    $this->flood = $flood;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('par_subscriptions.manager'),
      $container->get('flood')
    );
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
    return 'subscription_list_subscribe';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback($list = NULL) {
    return "Subscribe to {$this->getSubscriptionManager()->getListName($list)}";
  }

  /**
   * Subscribe to a list.
   *
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $list = NULL, $subscription_status = NULL) {
    if ($subscription_status === ParSubscriptionManager::SUBSCRIPTION_STATUS_SUBSCRIBED) {
      // Display a success message if the subscription has been processed.
      $form['verify'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('A verification link has been sent to your email address, you must complete this verification to activate your subscription.'),
        '#attributes' => ['class' => 'subscription-help'],
      ];

      $link_name = $this->currentUser()
        ->isAuthenticated() ? 'Go back to dashboard' : 'Go to the home page';
      $route = $this->currentUser()
        ->isAuthenticated() ? 'par_dashboards.dashboard' : '<front>';
      $form['back'] = [
        '#title' => $this->t($link_name),
        '#type' => 'link',
        '#url' => Url::fromRoute($route),
      ];
    }
    else {
      // Enter your email to unsubscribe.
      $form['email'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Enter your email'),
      ];
      $form['list'] = [
        '#type' => 'hidden',
        '#value' => $list,
      ];

      $form['actions']['subscribe'] = [
        '#type' => 'submit',
        '#name' => 'subscribe',
        '#value' => $this->t('Subscribe'),
        '#attributes' => [
          'class' => ['cta-submit', 'govuk-button'],
          'data-prevent-double-click' => 'true',
          'data-module' => 'govuk-button',
        ],
      ];
    }

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Add flood protection for unauthenticated users.
    $fid = implode(':', [$this->getRequest()->getClientIP(), $this->currentUser()->id()]);
    if ($this->currentUser()->isAnonymous() &&
      !$this->flood->isAllowed("par_subscriptions.{$this->getFormId()}", 10, 3600, $fid)) {
      $form_state->setErrorByName('text', $this->t(
        'Too many form submissions from your location.
        This IP address is temporarily blocked. Please try again later.'
      ));
      return;
    }

    $email = $form_state->getValue('email');
    $email_validator = \Drupal::service('email.validator');

    if (empty($email) || !$email_validator->isValid($email)) {
      $form_state->setErrorByName('email', "Please enter a valid email address.");
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Register flood protection.
    $fid = implode(':', [$this->getRequest()->getClientIP(), $this->currentUser()->id()]);
    $this->flood->register("par_subscriptions.{$this->getFormId()}", 3600, $fid);

    $action = $form_state->getTriggeringElement()['#name'];

    if ($action === 'subscribe') {
      $list = $form_state->getValue('list');
      $email = $form_state->getValue('email');
      $subscription = $this->getSubscriptionManager()
        ->createSubscription($list, $email);

      if ($subscription instanceof ParSubscriptionInterface) {
        // Silently subscribe to prevent enumeration attacks.
        $subscription->subscribe();
      }

      // Redirecting to the unsubscribe message with a universal 'unsubscribed' code.
      // This allows the message to be displayed without any codes being unsubscribed.
      $route_name = $this->getRouteMatch()->getRouteName();
      $parameters = ['subscription_status' => ParSubscriptionManager::SUBSCRIPTION_STATUS_SUBSCRIBED] +
        $this->getRouteMatch()->getRawParameters()->all();

      $form_state->setRedirect($route_name, $parameters);
    }

  }

}
