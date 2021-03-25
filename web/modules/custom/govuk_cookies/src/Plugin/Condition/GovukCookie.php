<?php

namespace Drupal\govuk_cookies\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\govuk_cookies\Form\CookieConsentForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Pass checks if the GOVUK cookie is present.
 *
 * @Condition(
 *   id = "govuk_cookie",
 *   label = @Translation("GOVUK Cookie"),
 *   description = @Translation("Allows selection dependant on whether a GOVUK cookie is set.")
 * )
 */
class GovukCookie extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Creates a new Cookie instance.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(RequestStack $request_stack, array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('request_stack'),
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#description' => 'Detection of GOVUK cookie.',
      '#default_value' => $this->configuration['enable'],
    ];

    $form = parent::buildConfigurationForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['enable'] = $form_state->getValue('enable');

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'enable' => FALSE,
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    if ($this->configuration['enable']) {
      $contexts[] = 'cookies:' . CookieConsentForm::COOKIE_NAME;
    }
    return $contexts;
  }

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   */
  public function evaluate() {
    return $this->configuration['enable'] &&
      $this->requestStack->getCurrentRequest()->cookies->has(CookieConsentForm::COOKIE_NAME);
  }

  /**
   * Provides a human readable summary of the condition's configuration.
   */
  public function summary() {
    $placeholders = [
      '@cookie_name' => $this->configuration['CookieConsentForm::COOKIE_NAME'],
    ];

    if ($this->configuration['enable']) {
      return $this->isNegated()
        ? $this->t('Cookie "@name" is NOT set', $placeholders)
        : $this->t('Cookie "@name" is set', $placeholders);
    }

    return '';
  }

}
