<?php

namespace Drupal\par_notification\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\State\StateInterface;
use Drupal\message\Entity\Message;
use Drupal\par_notification\ParLinkManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for handling link redirection requests that can't be resolved.
 */
class ParLinkNotFoundController extends ControllerBase {

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * Process a received request with help from the link manager.
   */
  public function build(Request $request) {
    $build = [];

    $build['info'] = [
      '#type' => 'markup',
      '#markup' => '<p>Thank you, this task has been completed, for more information please contact helpdesk.</p>',
    ];

    $dashboard_link = Link::createFromRoute('Go to the dashboard', 'par_dashboards.dashboard');
    $build['dashboard'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $dashboard_link->toString(),
      ]),
    ];

    return $build;
  }
}
