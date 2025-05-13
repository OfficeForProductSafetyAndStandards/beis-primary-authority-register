<?php

namespace Drupal\par_govuk_cookies\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
   * CookieBannerBlock Constructor.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected FormBuilderInterface $formBuilder,
    protected RequestStack $requestStack,
    protected ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder'),
      $container->get('request_stack'),
      $container->get('config.factory')
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
   * Returns generic default configuration for block plugins.
   *
   * @return array
   *   An associative array with the default configuration.
   */
  #[\Override]
  protected function baseConfigurationDefaults() {
    return ['label_display' => FALSE] + parent::baseConfigurationDefaults();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function build() {
    $build = [
      '#cache' => [
        'contexts' => [
          'session',
          'cookies:' . $this->getCookieName(),
        ],
      ],
      '#attached' => [
        'library' => [
          'par_govuk_cookies/par_govuk_cookies',
        ],
        'drupalSettings' => [
          'par_govuk_cookies' => [
            'banner' => [
              'cookie_name' => $this->getCookieName(),
              'cookie_types' => $this->getCookieTypes(),
              'cookie_properties' => [
                'expires' => \Drupal::time()->getRequestTime() + 31536000,
                'path' => '/',
                'domain' => ".{$this->requestStack->getCurrentRequest()->getHost()}",
                'secure' => FALSE,
                'samesite' => 'strict',
                'raw' => TRUE,
              ],
            ],
          ],
        ],
      ],
    ];

    if (!empty($this->getCookieName()) &&
        !$this->requestStack->getCurrentRequest()->cookies->has($this->getCookieName())) {
      // Main cookie message.
      $build['acknowledge'] = [
        '#theme' => 'par_govuk_cookies_banner',
      ];

      $cookie_types = ['analytics', 'functional', 'other'];
      $type = ($cookie_types && count($cookie_types) === 1 && current($cookie_types) === 'analytics') ?
        'analytics' :
        'additional';

      // Only display additional messages if non-essential cookies are being set.
      if (!empty($cookie_types)) {
        // Acceptance message.
        $build['accept'] = [
          '#theme' => 'par_govuk_cookies_banner',
          '#attributes' => [
            'id' => 'par-govuk-cookies-accepted',
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
          '#theme' => 'par_govuk_cookies_banner',
          '#attributes' => [
            'id' => 'par-govuk-cookies-rejected',
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

}
