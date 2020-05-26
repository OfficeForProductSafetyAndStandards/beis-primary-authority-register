<?php

namespace Drupal\par_flows\Controller;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_flows\ParDisplayTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Drupal\par_flows\Event\ParFlowEvents;

/**
* A controller for all styleguide page output.
*/
class ParBaseController extends ControllerBase implements ParBaseInterface {

  use ParRedirectTrait;
  use RefinableCacheableDependencyTrait;
  use ParDisplayTrait;
  use StringTranslationTrait;
  use ParControllerTrait;

  /**
   * The response cache kill switch.
   */
  protected $killSwitch;

  /**
   * The access result
   *
   * @var \Drupal\Core\Access\AccessResult
   */
  protected $accessResult;

  /**
   * Whether to skip redirection based on the 'destination' query parameter.
   *
   * This is typically done if we want to group two sets of forms together,
   * in which case we ignore the destination parameter for this form but
   * pass it on to the next route. Once the next form is completed it will be
   * redirected to the destination parameter.
   *
   * @var boolean
   */
  protected $skipQueryRedirection = FALSE;

  /*
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $negotiation
   *   The flow negotiator.
   * @param \Drupal\par_flows\ParFlowDataHandlerInterface $data_handler
   *   The flow data handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   *   The par form builder.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $kill_switch
   *   The page cache kill switch.
   */
  public function __construct(ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler, ParDataManagerInterface $par_data_manager, PluginManagerInterface $plugin_manager, KillSwitch $kill_switch) {
    $this->negotiator = $negotiator;
    $this->flowDataHandler = $data_handler;
    $this->parDataManager = $par_data_manager;
    $this->formBuilder = $plugin_manager;
    $this->killSwitch = $kill_switch;

    $this->setCurrentUser();

    // @TODO Move this to middleware to stop it being loaded when this controller
    // is contructed outside a request for a route this controller resolves.
    try {
      $this->getFlowNegotiator()->getFlow();

      $this->loadData();
    } catch (ParFlowException $e) {

    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('par_flows.negotiator'),
      $container->get('par_flows.data_handler'),
      $container->get('par_data.manager'),
      $container->get('plugin.manager.par_form_builder'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user.roles', 'route'];
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = []) {
    $this->initializeFlow();

    // Add all the registered components to the form.
    foreach ($this->getComponents() as $component) {
      // If there's is a cardinality parameter present display only this item.
      $cardinality = $this->getFlowDataHandler()->getParameter('cardinality');
      $index = isset($cardinality) ? (int) $cardinality : NULL;

      // Handle instances where FormBuilderInterface should return a redirect response.
      $plugin = $this->getFormBuilder()->getPluginElements($component, $build, $index);
      if ($plugin instanceof RedirectResponse) {
        return $plugin;
      }
    }

    // Add all the action links.
    if ($this->getFlowNegotiator()->getFlow()->hasAction('done')) {
      $done_url = $this->getProceedingUrl('done');
      $done_link = $done_url ? Link::fromTextAndUrl('Done', $done_url) : NULL;
      $build['done'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $done_link->toString(),
        ]),
      ];
    }
    else {
      if ($this->getFlowNegotiator()->getFlow()->hasAction('next')) {
        $next_url = $this->getProceedingUrl('next');
        $next_link = $next_url ? Link::fromTextAndUrl('Continue', $next_url) : NULL;
        $build['next'] = [
          '#type' => 'markup',
          '#prefix' => '<div class="form-group">',
          '#suffix' => '</div>',
          '#markup' => t('@link', [
            '@link' => $next_link->toString(),
          ]),
        ];
      }

      if ($this->getFlowNegotiator()->getFlow()->hasAction('cancel')) {
        $cancel_url = $this->getProceedingUrl('cancel');
        $cancel_link = $cancel_url ? Link::fromTextAndUrl('Cancel', $cancel_url) : NULL;
        $build['cancel'] = [
          '#type' => 'markup',
          '#prefix' => '<div>',
          '#suffix' => '</div>',
          '#markup' => t('@link', [
            '@link' => $cancel_link->toString(),
          ]),
        ];
      }
    }

    $cache = [
      '#cache' => [
        'contexts' => $this->getCacheContexts(),
        'tags' => $this->getCacheTags(),
        'max-age' => $this->getCacheMaxAge(),
      ]
    ];

    return $build + $cache;
  }

  public function getProceedingUrl($action) {
    $query = $this->getCurrentRequest()->query;

    // All links other than cancel should display as primary buttons.
    switch($action) {
      case 'cancel':
        $route_options = [];

        break;

      default:
        $route_options = ['attributes' => ['class' => 'button']];

    }

    // @TODO, need to convert all these defaults ('destination', 'skipQueryRedirection')
    // to form listeners so that they can happen within the 'progresRoute()'
    // method and therefore everywhere we call this method.


    // Determine whether to use the 'destination' query parameter
    // to determine redirection preferences.
    if ($this->skipQueryRedirection && $query->has('destination')) {
      $route_options['query']['destination'] = $query->get('destination');
      $query->remove('destination');
    }

    // 1) Use the destination parameter if it redirects to a route within the flow.
    // This should not apply when cancelling the current flow.
    if ($query->has('destination')) {
      $destination = $query->get('destination');
      $destination_url = $this->getPathValidator()->getUrlIfValid($destination);

      if ($destination_url && $destination_url instanceof Url && $destination_url->isRouted()
        && $this->getFlowNegotiator()->routeInFlow($destination_url->getRouteName())) {
        $route_name = $destination_url->getRouteName();
        $route_params = $destination_url->getRouteParameters();
      }
    }

    // 2) Get the next available route in the flow.
    try {
      if (!isset($route_name)) {
        $route_obj = $this->getFlowNegotiator()->getFlow()->progressRoute($action);
        $route_name = $route_obj->getRouteName();
        $route_params = $this->getRouteParams();
      }
    }
    catch (ParFlowException $e) {

    }
    catch (RouteNotFoundException $e) {

    }

    // 3) If the next route could not be found in the flow then
    // return to the entry route if one was specified.
    if (!isset($route_name) && $url = $this->getEntryUrl()) {
      $route_name = $url->getRouteName();
      $route_params = $url->getRouteParameters();

      // Delete form storage.
      // @TODO We could choose to delete the store if we're completing the journey.
      // $this->getFlowDataHandler()->deleteStore();
    }

    // 4) Allow flow modules to alter the route as required.
    $current_route = \Drupal::routeMatch();
    $matched_url = isset($route_name) && isset($route_params) ? Url::fromRoute($route_name, $route_params, $route_options) : NULL;
    $event = new ParFlowEvent($this->getFlowNegotiator()->getFlow(), $current_route, $matched_url, NULL);

    switch($action) {
      case 'cancel':
        $this->getEventDispatcher()->dispatch(ParFlowEvents::FLOW_CANCEL, $event);

        break;

      default:
        $this->getEventDispatcher()->dispatch(ParFlowEvents::FLOW_SUBMIT . ":$action", $event);

    }
    $url = $event->getUrl();

    // 5) We need a backup route in case no other routes can be found.
    if (!$url || !$url instanceof Url) {
      $url = Url::fromRoute('par_dashboards.dashboard', []);
    }

    // Delete form storage.
    if ($action === 'cancel') {
      $this->getFlowDataHandler()->deleteStore();
    }

    return $url;
  }

  /**
   * Access callback
   * Useful for custom business logic for access.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   *
   * @see \Drupal\Core\Access\AccessResult
   *   The options for callback.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    return $this->accessResult ? $this->accessResult : AccessResult::allowed();
  }

}



