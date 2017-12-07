<?php

namespace Drupal\par_data\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;

/**
 * Provides a default argument for contextual filters.
 *
 * @ingroup views_argument_default_plugins
 *
 * @ViewsArgumentDefault(
 *   id = "par_person_authorities",
 *   title = @Translation("PAR Person Authorities")
 * )
 */
class ParPersonAuthorities extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

  protected $par_data_manager;

  /**
   * {@inheritdoc}
   */ 
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->par_data_manager = \Drupal::service('par_data.manager');
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
  public function getArgument() {
    // Load current user and check if user has bypass permission.
    $account = User::load(\Drupal::currentUser()->id());
    if ($account->hasPermission('bypass par_data membership')) {
      return 'all';
    }

    // Get current user PAR Authorities.
    $user_authorities = array_keys($this->par_data_manager->hasMembershipsByType($account, 'par_data_authority'));

    // Contextual filters expect "+" for OR.
    return implode("+", $user_authorities);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user'];
  }

}
