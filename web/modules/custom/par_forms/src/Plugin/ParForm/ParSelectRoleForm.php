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
      $account = User::Load($this->getFlowDataHandler()->getCurrentUser()->id());
    }

    $roles = [];
    if ($account && $account->hasPermission('create organisation user')) {
      $roles[] = Role::load('par_organisation');
    }
    if ($account && $account->hasPermission('create authority user')) {
      $roles[] = Role::load('par_authority');
    }
    if ($account && $account->hasPermission('create enforcement user')) {
      $roles[] = Role::load('par_enforcement');
    }
    if ($account && $account->hasPermission('create helpdesk user')) {
      $roles[] = Role::load('par_helpdesk');
    }

    if (!empty($roles)) {
      $role_options = $this->getParDataManager()->getEntitiesAsOptions($roles, []);
      $this->getFlowDataHandler()->setFormPermValue("roles_options", $role_options);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Get all the allowed authorities.
    $role_options = $this->getFlowDataHandler()->getFormPermValue('roles_options');

    // If there isn't a choice go to the next page.
    if (count($role_options) <= 0) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => "Would you like to give this person a user account so that they can sign in to the Primary Authority Register?<br>If you choose not to this person can still be listed as a contact but won't be able to view any of the information or interact with the Primary Authority Register.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['role'] = [
      '#type' => 'radios',
      '#title' => t('Choose what type of user this person is'),
      '#options' => $role_options + ["<i>Don't create a user, just add the contact details</i>"],
      '#default_value' => $this->getDefaultValuesByKey("role", $cardinality, key($role_options)),
      '#attributes' => ['class' => ['form-group']],
    ];

    return $form;
  }
}
