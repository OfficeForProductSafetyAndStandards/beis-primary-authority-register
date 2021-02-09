<?php

namespace Drupal\par_subscriptions\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
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
   * Verify a subscription to a list.
   *
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


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
