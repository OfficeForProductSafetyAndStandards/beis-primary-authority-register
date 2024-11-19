<?php

namespace Drupal\par_govuk_cookies\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CookieSettingsForm extends ConfigFormBase {

  /**
   * CookieSettingsForm Constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_govuk_cookies_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['par_govuk_cookies.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('par_govuk_cookies.settings');

    $form['cookies'] = [
      '#type' => 'details',
      '#title' => $this->t('Cookie settings'),
      '#open' => TRUE,
    ];

    $form['cookies']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookie name'),
      '#description' => $this->t('Set the cookie name to record user consent.'),
      '#required' => TRUE,
      '#default_value' => $config->get('name') ?? '_cookie_policy',
    ];

    $form['cookies']['types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Cookie types'),
      '#description' => $this->t('List any non-essential cookies used by this site.'),
      '#options' => [
        'essential' => 'Essential',
        'usage' => 'Usage',
        'campaigns' => 'Campaigns'],
      '#required' => TRUE,
      '#default_value' => $config->get('types') ?? [],
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
    $config = $this->config('par_govuk_cookies.settings');
    $config->set('name', $form_state->getValue('name'))
      ->set('types', array_keys(array_filter($form_state->getValue('types'))))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
