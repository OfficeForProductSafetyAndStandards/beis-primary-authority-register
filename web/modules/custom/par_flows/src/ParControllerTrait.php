<?php

namespace Drupal\par_flows;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

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
    foreach ($this->getFlowNegotiator()->getFlow()->getCurrentStepComponents() as $weight => $component) {
      if ($plugin = $this->getFormBuilder()->createInstance($component)) {
        $this->components[$weight] = $plugin;
      }
    }

    return $this->components;
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
  public function getflowDataHandler() {
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
   * Returns the default title.
   */
  public function getDefaultTitle() {
    return $this->defaultTitle;
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    // Load data for all the registered components of the form.
    foreach ($this->getComponents() as $weight => $component) {
      $component->loadData();
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
