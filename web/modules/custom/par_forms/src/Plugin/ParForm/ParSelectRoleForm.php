<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  public function defaultConfiguration(): array {
    return ['roles' => ['par_enforcement', 'par_authority', 'par_authority_manager', 'par_organisation', 'par_helpdesk', 'senior_administration_officer']] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    if ($this->getFlowDataHandler()->getCurrentUser()->isAuthenticated()) {
      $current_user = User::Load($this->getFlowDataHandler()->getCurrentUser()->id());
    }
    else {
      $current_user = NULL;
    }

    // Additional configuration to determine which roles can be selected.
    $allowed_roles = isset($this->getConfiguration()['roles']) ? (array) $this->getConfiguration()['roles'] : [];

    $account = $this->getFlowDataHandler()->getParameter('user');
    // Some roles can only be set if the user has memberships in an authority
    // or an organisation, it is up to the calling form to determine this.
    $user_has_organisation = $this->getFlowDataHandler()->getFormPermValue("user_has_organisation") ?? TRUE;
    $user_has_authority = $this->getFlowDataHandler()->getFormPermValue("user_has_authority") ?? TRUE;

    $roles = [];
    if ($current_user && array_search('par_organisation', $allowed_roles) !== FALSE
      && $current_user->hasPermission('assign par_organisation role') && $user_has_organisation) {
      $roles['par_organisation'] = Role::load('par_organisation');
    }
    if ($current_user && array_search('par_authority', $allowed_roles) !== FALSE
      && $current_user->hasPermission('assign par_authority role') && $user_has_authority) {
      $roles['par_authority'] = Role::load('par_authority');
    }
    if ($current_user && array_search('par_authority', $allowed_roles) !== FALSE
      && $current_user->hasPermission('assign par_authority_manager role') && $user_has_authority) {
      $roles['par_authority_manager'] = Role::load('par_authority_manager');
    }
    if ($current_user && array_search('par_enforcement', $allowed_roles) !== FALSE
      && $current_user->hasPermission('assign par_enforcement role') && $user_has_authority) {
      $roles['par_enforcement'] = Role::load('par_enforcement');
    }
    if ($current_user && array_search('par_helpdesk', $allowed_roles) !== FALSE
      && $current_user->hasPermission('assign par_helpdesk role')) {
      $roles['par_helpdesk'] = Role::load('par_helpdesk');
    }
    if ($current_user && array_search('senior_administration_officer', $allowed_roles) !== FALSE
      && $current_user->hasPermission('assign senior_administration_officer role')) {
      $roles['senior_administration_officer'] = Role::load('senior_administration_officer');
    }

    if (!empty($roles)) {

      if ($account) {
        $this->getFlowDataHandler()->setFormPermValue("existing_user", $account->label());

        foreach (array_keys($roles) as $option) {
          if ($account->hasRole($option)) {
            $this->getFlowDataHandler()->setFormPermValue("default_role", $option);
            break;
          }
        }
      }

      $role_options = $this->getParDataManager()->getEntitiesAsOptions($roles, []);

      $this->getFlowDataHandler()->setFormPermValue("user_required", FALSE);
    }

    $this->getFlowDataHandler()->setFormPermValue("roles_options", !empty($role_options) ? $role_options : []);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $role_options = $this->getFlowDataHandler()->getFormPermValue('roles_options');

    // Get the default role selection.
    $default_option = $this->getDefaultValuesByKey("default_role", $index, key($role_options));

    // If there is only one choice select it and go to the next page.
    if (count($role_options) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('role', key($role_options));
    }
    // If there isn't a choice go to the next page.
    if (count($role_options) <= 1) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => "Some of the roles given to a user are assigned automatically and you may not be able to change them.<br><br>The user may also have other roles that you are not permitted to change.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['role'] = [
      '#type' => 'radios',
      '#title' => t('Choose what type of user this person is'),
      '#options' => $role_options,
      '#default_value' => $this->getDefaultValuesByKey("role", $index, $default_option),
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    $form['role_options'] = [
      '#type' => 'hidden',
      '#value' => array_keys($role_options),
    ];

    return $form;
  }
}
