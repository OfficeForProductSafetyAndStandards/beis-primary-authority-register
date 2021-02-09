<?php

namespace Drupal\par_subscriptions\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Email;
use Drupal\par_subscriptions\ParSubscriptionManager;
use Drupal\par_subscriptions\ParSubscriptionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form controller for unsubscribing from subscription lists.
 */
class ParUnsubscribeForm extends FormBase  {

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
    return 'subscription_list_unsubscribe';
  }


  /**
   * Verify a subscription to a list.
   *
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $subscription_code = NULL) {
    $subscription = $this->getSubscriptionManager()->getSubscription($subscription_code);
    if ($subscription) {
      // Silently unsubscribe.
      $subscription->unsubscribe();
    }

    if ($subscription_code) {
      // If there's a subscription code, tell the user that the subscription
      // will have been removed if it existed, but do not allow enumeration of
      // existing subscriptions.
      $form['help'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('You have been unsubscribed from this mailing list.'),
        '#attributes' => ['class' => 'subscription-help'],
      ];
    }
    else {
      // Enter your email to unsubscribe.
      $form['email'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Enter your email'),
      ];

      $form['actions']['unsubscribe'] = [
        '#type' => 'submit',
        '#name' => 'unsubscribe',
        '#value' => $this->t('Unsubscribe'),
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
    $action = $form_state->getTriggeringElement()['#name'];

    if ($action === 'unsubscribe') {
      $email = $form_state->getValue('email');
      $subscription = $this->getSubscriptionManager()->getSubscriptionByEmail($email);

      if ($subscription) {
        // Silently unsubscribe.
        $subscription->unsubscribe();
      }

      // Redirecting to the unsubscribe message with a universal 'unsubscribed' code.
      // This allows the message to be displayed without any codes being unsubscribed.
      $route_name = $this->getRouteMatch()->getRouteName();
      $parameters = ['subscription_code' => ParSubscriptionManager::UNIVERSAL_UNSUBSCRIBE_CODE] +
        $this->getRouteMatch()->getRawParameters()->all();

      $form_state->setRedirect($route_name, $parameters);
    }
  }
}
