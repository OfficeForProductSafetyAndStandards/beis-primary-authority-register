<?php

namespace Drupal\par_notification\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\State\StateInterface;
use Drupal\message\Entity\Message;
use Drupal\par_notification\ParLinkManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for handling link manager requests.
 */
class ParLinkManagerController extends ControllerBase {

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\par_notification\ParLinkManager
   */
  protected $linkManager;

  /**
   * The current page route match object.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRoute;

  /**
   * Constructs a new HealthController object.
   *
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $kill_switch
   *   The page cache kill switch.
   * @param \Drupal\par_notification\ParLinkManager $link_manager
   *   The par notification link manager.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match.
   */
  public function __construct(KillSwitch $kill_switch, ParLinkManager $link_manager, CurrentRouteMatch $current_route_match) {
    $this->killSwitch = $kill_switch;
    $this->linkManager = $link_manager;
    $this->currentRoute = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('page_cache_kill_switch'),
      $container->get('plugin.manager.par_link_manager'),
      $container->get('current_route_match')
    );
  }

  /**
   * Process a received request with help from the link manager.
   */
  public function receive(Request $request, Message $message) {
    // Disable page cache
    $this->killSwitch->trigger();

    $response = $this->linkManager->receive($this->currentRoute, $message);

    return $response;
  }
}
