<?php

namespace Drupal\par_cookies\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list of essential cookies used by the site in a block.
 *
 * @Block(
 *   id = "essential_cookies",
 *   admin_label = @Translation("Essential Cookies"),
 *   category = @Translation("Custom"),
 * )
 */
class EssentialCookiesBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
      '#value' => $this->t('Essential cookies keep your information secure while you use Notify. We do not need to ask permission to use them.'),
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
        'name' => '_cookie_policy',
        'purpose' => 'Saves your cookie consent settings',
        'expires' => '1 year',
      ],
    ];
    $build['cookies']['#rows'][] = [
      'data' => [
        'name' => 'PARSESSID',
        'purpose' => 'Used to keep you signed in',
        'expires' => '20 days',
      ],
    ];

    return $build;
  }

}
