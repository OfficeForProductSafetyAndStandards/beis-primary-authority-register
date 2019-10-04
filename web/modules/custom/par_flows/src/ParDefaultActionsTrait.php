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
   * Primary action title
   *
   * Allows the primary action title to be overridden.
   */
  protected $primaryActionTitle;

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
   * Getter for retrieving the primary action title.
   */
  public function getPrimaryActionTitle($fallback = '') {
    return !empty($this->primaryActionTitle) ? $this->t($this->primaryActionTitle) : $this->t($fallback);
  }

  /**
   * Setter for the primary action title.
   */
  public function setPrimaryActionTitle($title = '') {
    $this->primaryActionTitle = $title;
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
      $this->setActions(array_diff(array_values($this->getActions()), [$action]));
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
      $merged = array_merge(array_values($this->getActions()), [$action]);
      $this->setActions($merged);
    }
  }

}
