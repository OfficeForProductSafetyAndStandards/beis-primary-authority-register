<?php

namespace Drupal\govuk_cookies\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\govuk_cookies\Form\CookieConsentForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Cookie Banner' block.
 *
 * @Block(
 *   id = "cookie_banner",
 *   admin_label = @Translation("Cookie Banner"),
 *   category = @Translation("Forms"),
 * )
 */
class CookieBannerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs a new MasqueradeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * Returns generic default configuration for block plugins.
   *
   * @return array
   *   An associative array with the default configuration.
   */
  protected function baseConfigurationDefaults() {
    return ['label_display' => FALSE] + parent::baseConfigurationDefaults();
  }

  /**
   * Gets the request object.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   The request object.
   */
  protected function getRequest() {
    if (!$this->requestStack) {
      $this->requestStack = \Drupal::service('request_stack');
    }
    return $this->requestStack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Maintain the correct cache contexts for this block.
    $build = [
      '#cache' => [
        'contexts' => [
          'session',
          'cookies:'.CookieConsentForm::COOKIE_NAME,
        ],
      ],
    ];

    if (!$this->getRequest()->cookies->has(CookieConsentForm::COOKIE_NAME)) {
      // Main cookie message.
      $build['acknowledge'] = [
        '#theme' => 'govuk_cookie_message',
      ];

      // @TODO Replace with configurable cookie types.
      $cookie_types = ['analytics', 'functional', 'other'];
      $type = ($cookie_types && count($cookie_types) === 1 && current($cookie_types) === 'analytics') ?
        'analytics' :
        'additional';

      // Only display additional messages if non-essential cookies are being set.
      if (!empty($cookie_types)) {
        // Acceptance message.
        $build['accept'] = [
          '#theme' => 'govuk_cookie_message',
          '#attributes' => [
            'id' => 'govuk-cookies-accepted',
            'role' => 'alert',
            'hidden' => TRUE,
          ],
          '#title' => NULL,
          '#message' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $this->t(
              'You’ve accepted @type cookies. You can <a href="/cookies">change your cookie settings</a> at any time.',
              ['@type' => $type]
            ),
          ],
          '#cookie_policy' => NULL,
        ];

        // Rejection message.
        $build['reject'] = [
          '#theme' => 'govuk_cookie_message',
          '#attributes' => [
            'id' => 'govuk-cookies-rejected',
            'role' => 'alert',
            'hidden' => TRUE,
          ],
          '#title' => NULL,
          '#message' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $this->t(
              'You’ve rejected @type cookies. You can <a href="/cookies">change your cookie settings</a> at any time.',
              ['@type' => $type]
            ),
          ],
          '#cookie_policy' => NULL,
        ];
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['my_block_settings'] = $form_state->getValue('my_block_settings');
  }

}
