<?php

namespace Drupal\par_cookies\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list of analytics cookies used by the site in a block.
 *
 * @Block(
 *   id = "analytics_cookies",
 *   admin_label = @Translation("Analytics Cookies"),
 *   category = @Translation("Custom"),
 * )
 */
class AnalyticsCookiesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new MasqueradeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $build['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('With your permission, we use Google Analytics to collect data about how you use Primary Authority. This information helps us to improve our service.'),
    ];

    $build['limitations'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Google is not allowed to use or share our analytics data with anyone. '),
    ];

    $build['cookies'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['govuk-form-group']],
      '#header' => [
        'Name',
        'Purpose',
        'Expires',
      ],
      '#empty' => $this->t("There are no analytics cookies used on this site."),
    ];

    $build['cookies']['#rows'][] = [
      'data' => [
        'name' => '_ga',
        'purpose' => 'Checks if you’ve visited Primary Authority before. This helps us count how many people visit our site',
        'expires' => '2 years',
      ],
    ];
    $build['cookies']['#rows'][] = [
      'data' => [
        'name' => '_gid',
        'purpose' => 'Checks if you’ve visited Primary Authority before. This helps us count how many people visit our site',
        'expires' => '24 hours',
      ],
    ];

    return $build;
  }

}
