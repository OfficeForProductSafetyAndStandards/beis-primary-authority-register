<?php

namespace Drupal\par_login\Plugin\Menu;

use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An account link that redirects to the dashboard.
 */
class ParAccountLink extends MenuLinkDefault {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new LoginLogoutMenuLink.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Menu\StaticMenuLinkOverridesInterface $static_override
   *   The static override storage.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StaticMenuLinkOverridesInterface $static_override, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $static_override);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu_link.static.overrides'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    if ($this->currentUser->isAuthenticated()) {
      return $this->currentUser->getDisplayName();
    }

    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteName() {
    if ($this->currentUser->hasPermission('access helpdesk')) {
      return 'par_help_desks_flows.helpdesk_dashboard';
    }
    else if ($this->currentUser->hasPermission('access par dashboard')) {
      return 'par_dashboards.dashboard';
    }

    return 'route:<no-link>';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user.roles:authenticated'];
  }

}
