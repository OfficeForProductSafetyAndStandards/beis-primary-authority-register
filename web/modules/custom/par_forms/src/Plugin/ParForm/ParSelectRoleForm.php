<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Role selection form plugin.
 *
 * @ParForm(
 *   id = "role_select",
 *   title = @Translation("Role selection.")
 * )
 */
class ParSelectRoleForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    if ($this->getFlowDataHandler()->getCurrentUser()->isAuthenticated()) {
      $current_user = User::Load($this->getFlowDataHandler()->getCurrentUser()->id());
    }
    else {
      $current_user = NULL;
    }

    $account = $this->getFlowDataHandler()->getParameter('user');

    $roles = [];
    if ($current_user && $current_user->hasPermission('create organisation user')) {
      $roles[] = Role::load('par_organisation');
    }
    if ($current_user && $current_user->hasPermission('create authority user')) {
      $roles[] = Role::load('par_authority');
    }
    if ($current_user && $current_user->hasPermission('create enforcement user')) {
      $roles[] = Role::load('par_enforcement');
    }
    if ($current_user && $current_user->hasPermission('create helpdesk user')) {
      $roles[] = Role::load('par_helpdesk');
    }

    if (!empty($roles)) {
      $role_options = $this->getParDataManager()->getEntitiesAsOptions($roles, []);

      if ($account) {
        $this->getFlowDataHandler()->setFormPermValue("existing_user", $account->label());
      }

      $this->getFlowDataHandler()->setFormPermValue("user_required", FALSE);
    }
    $this->getFlowDataHandler()->setFormPermValue("roles_options", !empty($role_options) ? $role_options : []);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Get all the allowed authorities.
    $role_options = $this->getFlowDataHandler()->getFormPermValue('roles_options');

    // If there is only one choice select it and go to the next page.
    if (count($role_options) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('role', key($role_options));
    }
    // If there isn't a choice go to the next page.
    if (count($role_options) <= 1) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => "Some of the roles given to a user are assigned automatically. If you are not a part of the same authorities and organisations as this user you may not be able to change all of the user's roles.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['role'] = [
      '#type' => 'radios',
      '#title' => t('Choose what type of user this person is'),
      '#options' => $role_options,
      '#default_value' => $this->getDefaultValuesByKey("role", $cardinality, key($role_options)),
      '#attributes' => ['class' => ['form-group']],
    ];

    return $form;
  }
}
