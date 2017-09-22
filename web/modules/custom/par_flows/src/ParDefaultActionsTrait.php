<?php

namespace Drupal\par_flows;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProvider;

trait ParDefaultActionsTrait {

  /**
   * Actions to be added to each form.
   *
   * How these actions will be interpreted is up to
   * the Controller responsible for rendering the
   * button or link.
   *
   * @see ParFormBase::buildForm()
   */
  protected $defaultActions;

  /**
   * Getter for retrieving the forms actions.
   */
  public function getActions() {
    return $this->defaultActions;
  }

  /**
   * Setter for the forms actions.
   */
  public function setActions($actions = []) {
    $this->defaultActions = $actions;
  }

  /**
   * Setter for the forms default actions.
   */
  public function setDefaultActions($actions = []) {
    if (empty($this->getActions())) {
      $this->defaultActions = $actions;
    }
  }

  /**
   * Getter for default form actions
   */
  public function hasAction($action) {
    return (!empty($this->getActions()) && in_array($action, $this->getActions()));
  }

  /**
   * Disable a default action.
   *
   * @param string $action
   *   Which action to cancel.
   */
  public function disableAction($action) {
    if (!empty($this->getActions()) && in_array($action, $this->getActions())) {
      $this->setActions(array_diff($this->getActions(), [$action]));
    }
  }

  /**
   * Enable a default action.
   *
   * @param string $action
   *   Which action to cancel.
   */
  public function enableAction($action) {
    if (empty($this->getActions()) || !in_array($action, $this->getActions())) {
      $this->setActions($this->getActions() + [$action]);
    }
  }

}
