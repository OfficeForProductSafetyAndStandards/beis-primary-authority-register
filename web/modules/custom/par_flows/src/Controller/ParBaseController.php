<?php

namespace Drupal\par_flows\Controller;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_data\ParDataManagerInterface;
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
use Symfony\Component\Routing\Route;

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
   * The access result
   *
   * @var \Drupal\Core\Access\AccessResult
   */
  protected $accessResult;

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
   */
  public function __construct(ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler, ParDataManagerInterface $par_data_manager, PluginManagerInterface $plugin_manager) {
    $this->negotiator = $negotiator;
    $this->flowDataHandler = $data_handler;
    $this->parDataManager = $par_data_manager;
    $this->formBuilder = $plugin_manager;

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
      $container->get('plugin.manager.par_form_builder')
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
      $build['done'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()->getNextLink('done', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
            ->setText($this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Done'))
            ->toString(),
        ]),
      ];
    }
    else {
      if ($this->getFlowNegotiator()->getFlow()->hasAction('next')) {
        $build['next'] = [
          '#type' => 'markup',
          '#prefix' => '<div class="form-group">',
          '#suffix' => '</div>',
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()->getNextLink('next', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
              ->setText($this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Continue'))
              ->toString(),
          ]),
        ];
      }

      if ($this->getFlowNegotiator()->getFlow()->hasAction('cancel')) {
        $build['cancel'] = [
          '#type' => 'markup',
          '#prefix' => '<div>',
          '#suffix' => '</div>',
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()->getPrevLink('cancel')
              ->setText('Cancel')
              ->toString(),
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



