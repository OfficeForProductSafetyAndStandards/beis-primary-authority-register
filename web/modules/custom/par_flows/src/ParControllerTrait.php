<?php

namespace Drupal\par_flows;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\par_forms\ParFormPluginInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

trait ParControllerTrait {

  /**
   * Default page title.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  protected $defaultTitle = 'Primary Authority Register';

  /**
   * Page title.
   *
   * @var string
   */
  protected $pageTitle;

  /**
   * The account for the current logged in user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $currentUser;

  /**
   * The form component plugins.
   *
   * @var \Drupal\Component\Plugin\PluginInspectionInterface[]
   */
  protected $components = [];

  /**
   * The flow negotiator.
   *
   * @var \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  protected $negotiator;

  /**
   * The flow data manager.
   *
   * @var \Drupal\par_flows\ParFlowDataHandlerInterface
   */
  protected $flowDataHandler;

  /**
   * The PAR data manager.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The PAR form builder.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $formBuilder;

  /**
   * Get the current user account.
   */
  public function getCurrentUser() {
    return $this->currentUser;
  }

  /**
   * Set the current user account.
   */
  public function setCurrentUser(AccountInterface $account = NULL) {
    if (\Drupal::currentUser()->isAuthenticated() && !$this->getCurrentUser()) {
      $id = $account ? $account->id() : \Drupal::currentUser()->id();
      $this->currentUser = User::load($id);
    }
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
  }

  /**
   * {@inheritdoc}
   */
  public function getComponents() {
    if (!empty($this->components)) {
      return $this->components;
    }

    // Load the plugins used to build this form.
    foreach ($this->getFlowNegotiator()->getFlow()->getCurrentStepComponents() as $key => $settings) {
      try {
        $plugin_name = $settings[ParFormPluginInterface::NAME_PROPERTY] ?? $key;
          if ($plugin = $this->getFormBuilder()->createInstance($plugin_name, $settings)) {
            $this->components[] = $plugin;
          }
      }
      catch (PluginException $e) {
        $this->getLogger($this->getLoggerChannel())->error($e);
      }
      catch (\TypeError $e) {
        $this->getLogger($this->getLoggerChannel())->error($e);
      }
    }

    return $this->components;
  }

  /**
   * {@inheritdoc}
   */
  public function getComponent($component_name) {
    foreach ($this->getComponents() as $component) {
      if ($component->getPluginId() === $component_name) {
        return $component;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFlowNegotiator() {
    return $this->negotiator;
  }

  /**
   * {@inheritdoc}
   */
  public function getFlowDataHandler() {
    return $this->flowDataHandler;
  }

  /**
   * {@inheritdoc}
   */
  public function getParDataManager() {
    return $this->parDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormBuilder() {
    return $this->formBuilder;
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  public function getEventDispatcher() {
    return \Drupal::service('event_dispatcher');
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Drupal\Core\Path\PathValidatorInterface
   */
  public function getPathValidator() {
    return \Drupal::service('path.validator');
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Drupal\Core\Routing\RouteProviderInterface
   */
  public function getRouteProvider() {
    return \Drupal::service('router.route_provider');
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   */
  public function getCurrentRequest() {
    return \Drupal::service('request_stack')->getCurrentRequest();
  }

  /**
   * Returns the default title.
   */
  public function getDefaultTitle() {
    return $this->defaultTitle;
  }

  /**
   * Initialise the flow with required entry points.
   */
  public function initializeFlow() {
    $entry_point = $this->getFlowDataHandler()->getMetaDataValue(ParFlowDataHandler::ENTRY_POINT);

    // If an entry point is not already get the referer.
    $referer = !$entry_point ? $this->getCurrentRequest()->headers->get('referer') : NULL;

    // Check the referer request was for the same site, do not redirect to other sites.
    $referer_request = $referer ? Request::create($referer) : NULL;
    $url = $referer_request && $referer_request->getHost() === $this->getCurrentRequest()->getHost() ?
      $this->getPathValidator()->getUrlIfValid($referer_request->getRequestUri()) : NULL;

    // Check that the url belongs to a drupal route and that it isn't in the current flow.
    if ($url && $url instanceof Url && $url->isRouted() && !$this->getFlowNegotiator()->routeInFlow($url->getRouteName())) {
      $this->getFlowDataHandler()->setMetaDataValue(ParFlowDataHandler::ENTRY_POINT, $referer_request->getRequestUri());
    }
  }

  /**
   * Get the entry route.
   *
   * @return \Drupal\Core\Url|NULL
   *   A Matched route.
   */
  public function getEntryUrl() {
    $entry_point = $this->getFlowDataHandler()->getMetaDataValue(ParFlowDataHandler::ENTRY_POINT);

    try {
      $url = $this->getPathValidator()->getUrlIfValid($entry_point);
    }
    catch (\InvalidArgumentException $e) {

    }

    if ($url && $url instanceof Url && $url->isRouted()) {
      return $url;
    }

    return NULL;
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    // Load data for all the registered components of the form.
    foreach ($this->getComponents() as $component) {
      $this->getFormBuilder()->loadPluginData($component);
    }
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    if (empty($this->pageTitle) &&
      $default_title = $this->getFlowNegotiator()->getFlow()->getDefaultTitle()) {
      return $default_title;
    }

    // Do we have a form flow subheader?
    if (!empty($this->getFlowNegotiator()->getFlow()->getDefaultSectionTitle()) &&
      !empty($this->pageTitle)) {
      $this->pageTitle = "{$this->getFlowNegotiator()->getFlow()->getDefaultSectionTitle()} | {$this->pageTitle}";
    }

    return $this->pageTitle;
  }

}
