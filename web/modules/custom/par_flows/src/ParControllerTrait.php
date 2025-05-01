<?php

namespace Drupal\par_flows;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_forms\ParFormPluginInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_forms\ParFormBuilderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Component\Plugin\PluginInspectionInterface;

trait ParControllerTrait {

  /**
   * Default page title.
   *
   * @var ?string
   */
  protected $defaultTitle = 'Primary Authority Register';

  /**
   * Page title.
   *
   * @var ?string
   */
  protected $pageTitle = NULL;

  /**
   * The account for the current logged in user.
   *
   * @var User
   */
  protected $currentUser;

  /**
   * The form component plugins.
   *
   * @var PluginInspectionInterface[]
   */
  protected array $components = [];

  /**
   * The flow negotiator.
   *
   * @var ParFlowNegotiatorInterface
   */
  protected ParFlowNegotiatorInterface $negotiator;

  /**
   * The flow data manager.
   *
   * @var ParFlowDataHandlerInterface
   */
  protected ParFlowDataHandlerInterface $flowDataHandler;

  /**
   * The PAR data manager.
   *
   * @var ParDataManagerInterface
   */
  protected ParDataManagerInterface $parDataManager;

  /**
   * The PAR form builder.
   *
   * @var ParFormBuilderInterface
   */
  protected $formBuilder;


  /**
   * The url generator used in par forms.
   *
   * @var UrlGeneratorInterface
   */
  protected UrlGeneratorInterface $urlGenerator;

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
        $plugin_name = ParFlow::getComponentName($key, $settings);

        // Store the plugin ID and namespace in the settings.
        $settings[ParFormPluginInterface::NAME_PROPERTY] ??= $plugin_name;
        $settings[ParFormPluginInterface::NAMESPACE_PROPERTY] ??= $key;

        if ($plugin = $this->getFormBuilder()->createInstance($plugin_name, $settings)) {
          $this->components[] = $plugin;
        }
      }
      catch (PluginException|\TypeError $e) {
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
      if ($component->getPluginNamespace() === $component_name) {
        return $component;
      }
    }

    return NULL;
  }

  /**
   * @return \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  public function getFlowNegotiator() {
    return $this->negotiator;
  }

  /**
   * @return \Drupal\par_flows\ParFlowDataHandlerInterface
   */
  public function getFlowDataHandler() {
    return $this->flowDataHandler;
  }

  /**
   * @return \Drupal\par_data\ParDataManagerInterface
   */
  public function getParDataManager() {
    return $this->parDataManager;
  }

  /**
   * @return \Drupal\par_forms\ParFormBuilderInterface
   */
  public function getFormBuilder(): ParFormBuilderInterface {
    return $this->formBuilder;
  }

  /**
   * @return UrlGeneratorInterface
   */
  public function getUrlGenerator() {
    return $this->urlGenerator;
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
    catch (\InvalidArgumentException) {

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
      $this->getFormBuilder()->loadData($component);
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
