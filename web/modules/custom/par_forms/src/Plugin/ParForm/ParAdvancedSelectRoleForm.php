<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\par_roles\ParRoleException;
use Drupal\par_roles\ParRoleManager;
use Drupal\par_roles\ParRoleManagerInterface;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Role selection form plugin.
 *
 * @ParForm(
 *   id = "advanced_role_select",
 *   title = @Translation("Advanced role selection.")
 * )
 */
class ParAdvancedSelectRoleForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function defaultConfiguration(): array {
    return ['roles' => $this->getParRoleManager()->getAllRoles()] + parent::defaultConfiguration();
  }

  /**
   * Get the PAR Role manager.
   */
  protected function getParRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    if ($this->getFlowDataHandler()->getCurrentUser()->isAuthenticated()) {
      $current_user = User::Load($this->getFlowDataHandler()->getCurrentUser()->id());
    }
    else {
      $current_user = NULL;
    }

    // Get the form cache ID for membership selection.
    $membership_selection_cid = $this->getFlowNegotiator()->getFormKey('select_memberships');
    // Lookup whether any memberships have been assigned in this journey.
    $assigned_membership_ids = array_filter([
      'par_data_authority' => $this->getFlowDataHandler()->getTempDataValue("par_data_authority_id", $membership_selection_cid),
      'par_data_organisation' => $this->getFlowDataHandler()->getTempDataValue("par_data_organisation_id", $membership_selection_cid),
    ]);

    // Get the user account.
    $account = $this->getFlowDataHandler()->getParameter('user');
    // Get the person.
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    // Try to get the account from the person.
    if (!$account && $par_data_person instanceof ParDataPersonInterface) {
      $account = $par_data_person->getUserAccount();
    }

    // All allowed roles.
    $roles = $this->defaultConfiguration()['roles'] ?? [];
    $all_roles = [];
    $general_roles = [];
    $institution_roles = [
      'par_data_authority' => [],
      'par_data_organisation' => [],
    ];
    foreach ($roles as $rid) {
      $role = Role::load($rid);

      // If the user doesn't have permission ignore the role.
      if (!$role || !$current_user->hasPermission("assign {$rid} role")) {
        continue;
      }

      // Record the roles the user can change.
      $all_roles[$rid] = $role?->label();

      // If the role is an institution role.
      if (in_array($rid, $this->getParRoleManager()->getAllInstitutionRoles())) {
        // Get the institution type from the role.
        $institution_type = $this->getParRoleManager()->getInstitutionTypeByRole($rid);

        // Only process this role if memberships have been assigned at a previous step.
        if (!empty($assigned_membership_ids) &&
          !empty($assigned_membership_ids[$institution_type])) {
            $institution_roles[$institution_type][$rid] = $role?->label();
        }
        // Or if the user has memberships to this institution type.
        if (empty($assigned_membership_ids) && $account instanceof UserInterface &&
          $this->getParRoleManager()->hasInstitutions($account, $institution_type)) {
            $institution_roles[$institution_type][$rid] = $role?->label();
        }
        // Or if the person has memberships to this institution type.
        else if (empty($assigned_membership_ids) && !$account && $par_data_person instanceof ParDataPersonInterface &&
          $par_data_person->hasInstitutions($institution_type)) {
            $institution_roles[$institution_type][$rid] = $role?->label();
        }
      }
      // If the role is a general role.
      else {
        $general_roles[$rid] = $role?->label();
      }
    }

    // Get all the roles available for selection.
    $this->getFlowDataHandler()->setFormPermValue("all_roles_options", $all_roles);
    $this->getFlowDataHandler()->setFormPermValue("general_roles_options", $general_roles);
    $this->getFlowDataHandler()->setFormPermValue("institution_roles_options", array_filter($institution_roles));

    // Set the user's current roles.
    $user_general_roles = array_intersect(
      array_keys($general_roles) ?? [],
      $account?->getRoles() ?? []
    );
    $user_institution_roles = [
      'par_data_authority' => array_intersect(
        array_keys($institution_roles['par_data_authority']) ?? [],
          $account?->getRoles() ?? []
      ),
      'par_data_organisation' => array_intersect(
        array_keys($institution_roles['par_data_organisation']) ?? [],
        $account?->getRoles() ?? []
      ),
    ];

    // Get all the roles that the user already has.
    $this->getFlowDataHandler()->setFormPermValue("general", $user_general_roles);
    $this->getFlowDataHandler()->setFormPermValue("par_data_organisation", $user_institution_roles['par_data_organisation']);
    $this->getFlowDataHandler()->setFormPermValue("par_data_authority", $user_institution_roles['par_data_authority']);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $all_role_options = $this->getFlowDataHandler()->getFormPermValue('all_roles_options');


    if (empty($all_role_options)) {
      $form['intro'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => 'You do not have permission to assign any roles to this user.'
      ];
    }
    else {
      $form['intro'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => 'Some of the roles given to a user are assigned automatically, and you may not be able to change them.<br><br>The user may also have other roles that you are not permitted to change.',
      ];
    }

    $form['roles'] = [
      '#type' => 'container',
      '#title' => t('Choose which roles this user should have'),
      '#title_tag' => 'h2',
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    // Add all the available roles to the form for later processing.
    $form['roles']['available'] = [
      '#type' => 'hidden',
      '#value' => array_keys($all_role_options),
    ];

    // General rules.
    $general_role_options = $this->getFlowDataHandler()->getFormPermValue('general_roles_options');
    if (!empty($general_role_options)) {
      $form['roles']['general'] = [
        '#type' => 'checkboxes',
        '#title' => t('Would you like to assign any general roles?'),
        '#description' => t('These rules apply to the entire Primary Authority Register, and not to any specific authority or organisation.'),
        '#title_tag' => 'h2',
        '#options' => $general_role_options,
        '#default_value' => $this->getDefaultValuesByKey('general', $index, []),
        '#return_value' => 'on',
        '#attributes' => ['class' => ['govuk-form-group']],
      ];
    }

    // Institution rules.
    $institution_role_options = $this->getFlowDataHandler()->getFormPermValue("institution_roles_options");
    foreach ($institution_role_options as $institution_type => $institution_roles) {
      if (empty($institution_roles)) {
        continue;
      }

      $form['roles'][$institution_type] = [
        '#type' => 'checkboxes',
        '#title' => t('Would you like to assign any %institution roles?', ['%institution' => ParRoleManager::INSTITUTION_LABEL[$institution_type]]),
        '#description' => t('These rules apply only to the %institution that the user is a member of.', ['%institution' => ParRoleManager::INSTITUTION_LABEL[$institution_type]]),
        '#title_tag' => 'h2',
        '#options' => $institution_roles,
        '#default_value' => $this->getDefaultValuesByKey($institution_type, $index, []),
        '#return_value' => 'on',
        '#attributes' => ['class' => ['govuk-form-group']],
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    // Get all the roles available for selection.
    $general_role_options = $this->getFlowDataHandler()->getFormPermValue("general_roles_options");
    $institution_role_options = $this->getFlowDataHandler()->getFormPermValue("institution_roles_options");

    // Get the values.
    $available_element = $this->getElement($form, ['roles', 'available'], $index);
    $general_element = $this->getElement($form, ['roles', 'general'], $index);
    $all_roles = $available_element ?
      array_filter($form_state->getValue($available_element['#parents'])) : [];
    $general_roles = $general_element ?
      array_filter($form_state->getValue($general_element['#parents'])) : [];
    // Get the institution values.
    foreach ($institution_role_options as $institution_type => $institution_roles) {
      $element_name = "{$institution_type}_element";
      $value_name = "{$institution_type}_roles";
      ${$element_name} = $this->getElement($form, ['roles', $institution_type], $index);
      ${$value_name} = ${$element_name} ?
        array_filter($form_state->getValue(${$element_name}['#parents'])) : [];
    }

    // Check 1: There must be some roles selected.
    if (!empty($all_roles) &&
      empty($general_roles) &&
      empty($par_data_authority_roles) &&
      empty($par_data_organisation_roles)) {

      $message = 'You must select at least one role.';
      if ($general_element) {
        $this->setError($form, $form_state, $general_element, $message);
      }
      foreach ($institution_role_options as $institution_type => $institution_roles) {
        // Only set the error if there are some institution roles.
        if (!empty($institution_roles)) {
          $element_name = "{$institution_type}_element";
          if (${$element_name}) {
            $this->setError($form, $form_state, ${$element_name}, $message);
          }
        }
      }
    }

    // Check that there is at least one institution role for each institution the user has.
    foreach ($institution_role_options as $institution_type => $institution_roles) {
      $element_name = "{$institution_type}_element";
      $value_name = "{$institution_type}_roles";

      $message = $this->t(
        'You must select at least one @institution role.',
        ['@institution' => ParRoleManager::INSTITUTION_LABEL[$institution_type]],
      );
      if (!empty($institution_roles) && empty(${$value_name})) {
        $this->setError($form, $form_state, ${$element_name}, $message);
      }
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
