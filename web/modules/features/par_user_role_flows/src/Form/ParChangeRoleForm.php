<?php

namespace Drupal\par_user_role_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_roles\ParRoleException;
use Drupal\par_roles\ParRoleManager;
use Drupal\par_roles\ParRoleManagerInterface;

/**
 * A controller for blocking user accounts.
 */
class ParChangeRoleForm extends ParBaseForm {

  /**
   * Get Date Formatter.
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * Get the PAR Role manager.
   */
  protected function getParRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = "Change roles";

  /**
   * Implements loadData().
   */
  public function loadData() {
    $user = $this->getFlowDataHandler()->getParameter('user');
    if (!$user && $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $user = $par_data_person->getUserAccount();
      $this->getFlowDataHandler()->setParameter('user', $user);
    }

    if ($user) {
      $this->getFlowDataHandler()->setFormPermValue('email', $user->getEmail());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $user = $this->getFlowDataHandler()->getParameter('user');

    $all_roles = $this->getParRoleManager()->getAllRoles();
    $roles = array_filter((array) $this->getFlowDataHandler()->getTempDataValue('general'));
    foreach (ParRoleManager::INSTITUTION_ROLES as $institution_type => $institution_roles) {
      $institution_roles = array_filter((array) $this->getFlowDataHandler()->getTempDataValue($institution_type));
      $roles += $institution_roles;
    }

    // Add Roles.
    foreach ($roles as $role) {
      try {
        $user = $this->getParRoleManager()->addRole($user, $role);
      }
      catch (ParRoleException $ignore) {

      }
    }

    // Remove Roles.
    $remove_roles = array_diff($all_roles, $roles);
    foreach ($remove_roles as $role) {
      try {
        $user = $this->getParRoleManager()->removeRole($user, $role);
      }
      catch (ParRoleException $ignore) {

      }
    }

    // Save the user with the new roles added.
    if ($user && $user->save()) {
      // Also invalidate the user account cache if there is one.
      \Drupal::entityTypeManager()->getStorage('user')->resetCache([$user->id()]);

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('User account could not be blocked for: %account');
      $replacements = [
        '%account' => $user->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
