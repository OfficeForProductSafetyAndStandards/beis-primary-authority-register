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
use Drupal\Core\Routing\UrlGeneratorInterface;
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
   *
   * @var KillSwitch $killSwitch
   */
  protected KillSwitch $killSwitch;

  /**
   * The access result.
   *
   * @var ?AccessResult $accessResult
   */
  protected ?AccessResult $accessResult = NULL;

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
  public function __construct(ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler, ParDataManagerInterface $par_data_manager, PluginManagerInterface $plugin_manager, KillSwitch $kill_switch, UrlGeneratorInterface $url_generator) {
    $this->negotiator = $negotiator;
    $this->flowDataHandler = $data_handler;
    $this->parDataManager = $par_data_manager;
    $this->formBuilder = $plugin_manager;
    $this->killSwitch = $kill_switch;
    $this->urlGenerator = $url_generator;

    $this->setCurrentUser();
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
      $container->get('page_cache_kill_switch'),
      $container->get('url_generator')
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
      // If there is a cardinality parameter present display only this item.
      $index = $this->getFlowDataHandler()->getParameter('cardinality');

      // Build the plugin.
      $plugin = $this->getFormBuilder()->build($component, $index);

      // Merge the component elements into the build array.
      $build = array_merge($build, $plugin);
    }

    // Add all the action links.
    if ($this->getFlowNegotiator()->getFlow()->hasAction('done')) {
      $done_url = $this->getProceedingUrl('done');
      $build['done'] = [
        '#type' => 'link',
        '#title' => 'Done',
        '#url' => $done_url,
        '#attributes' => [
          'class' => ['govuk-button', 'govuk-form-group'],
          'role' => 'button'
        ],
      ];
    }
    else {
      if ($this->getFlowNegotiator()->getFlow()->hasAction('next')) {
        $next_url = $this->getProceedingUrl('next');
        $build['next'] = [
          '#type' => 'link',
          '#title' => 'Continue',
          '#url' => $next_url,
          '#attributes' => [
            'class' => ['govuk-button', 'govuk-form-group'],
            'role' => 'button'
          ],
        ];
      }

      if ($this->getFlowNegotiator()->getFlow()->hasAction('cancel')) {
        $cancel_url = $this->getProceedingUrl('cancel');
        $build['cancel'] = [
          '#type' => 'link',
          '#title' => 'Cancel',
          '#url' => $cancel_url,
          '#attributes' => [
            'class' => ['cta-cancel', 'govuk-button', 'govuk-button--secondary'],
            'role' => 'button'
          ],
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
    // Determine the appropriate redirection url.
    $url = $this->getFlowNegotiator()->getFlow()->progress($action);

    // All links other than cancel should display as primary buttons.
    switch($action) {
      case 'cancel':
        $route_options = [];
        break;

      default:
        $route_options = ['attributes' => ['class' => ['button']]];
    }

    if ($url && $url instanceof Url) {
      $url->mergeOptions($route_options);
    }

    // @TODO Cancelling a flow through a link cannot delete the flow data.

    return $url;
  }

  /**
   * Default access callback.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account): AccessResult {
    return $this->accessResult instanceof AccessResult ? $this->accessResult : AccessResult::allowed();
  }

}



