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
use Drupal\user\Form\UserLoginForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for handling link redirection requests when the user is not signed in.
 */
class ParAccessDeniedController extends ControllerBase {

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * Get form builder service.
   *
   * @return \Drupal\Core\Form\FormBuilderInterface
   */
  public function getFormBuilder() {
    return \Drupal::formBuilder();
  }

  /**
   * Process a received request with help from the link manager.
   */
  public function build(Request $request) {
    $build = [];

    $build['login'] = $this->getFormBuilder()->getForm(UserLoginForm::class);

    $dashboard_link = Link::createFromRoute('Request an invitation', 'par_dashboards.dashboard');
    $build['invitation'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $dashboard_link->toString(),
      ]),
    ];

    return $build;
  }
}
