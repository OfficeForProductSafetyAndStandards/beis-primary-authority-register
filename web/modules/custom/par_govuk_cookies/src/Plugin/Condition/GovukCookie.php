<?php

namespace Drupal\par_govuk_cookies\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Pass checks if the GOVUK cookie is present.
 *
 * @Condition(
 *   id = "par_govuk_cookies",
 *   label = @Translation("PAR GOVUK Cookie"),
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
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

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
  public function __construct(RequestStack $request_stack, ConfigFactoryInterface $config_factory, array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->requestStack = $request_stack;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('request_stack'),
      $container->get('config.factory'),
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getCookieName() {
    return $this->configFactory->get('par_govuk_cookies.settings')->get('name');
  }

  /**
   * {@inheritdoc}
   */
  protected function getCookieTypes() {
    return $this->configFactory->get('par_govuk_cookies.settings')->get('types');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#description' => 'Detection of PAR GOVUK cookie.',
      '#default_value' => $this->configuration['enable'],
    ];

    $form = parent::buildConfigurationForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['enable'] = $form_state->getValue('enable');

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function defaultConfiguration() {
    return [
        'enable' => FALSE,
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    if ($this->configuration['enable']) {
      $contexts[] = 'cookies:' . $this->getCookieName();
    }
    return $contexts;
  }

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   */
  #[\Override]
  public function evaluate() {
    // Ignore this condition if not enabled.
    if (!$this->configuration['enable']) {
      return true;
    }

    // Determine whether the cookie is set.
    return $this->configuration['enable'] &&
      $this->requestStack->getCurrentRequest()->cookies->has($this->getCookieName());
  }

  /**
   * Provides a human readable summary of the condition's configuration.
   */
  #[\Override]
  public function summary() {
    $placeholders = [
      '@cookie_name' => $this->configuration[$this->getCookieName()],
    ];

    if ($this->configuration['enable']) {
      return $this->isNegated()
        ? $this->t('Cookie "@name" is NOT set', $placeholders)
        : $this->t('Cookie "@name" is set', $placeholders);
    }

    return '';
  }

}
