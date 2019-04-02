<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\NestedArray;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Form for selecting memberships available to the current user.
 *
 * @ParForm(
 *   id = "memberships_select",
 *   title = @Translation("Membership selection.")
 * )
 */
class ParSelectMembershipsForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $user_organisations = [];
    $user_authorities = [];

    // If the person being edited has a user account get all the user's memberships.
    // Otherwise use the person being edited if there is one.
    if ($account = $this->getFlowDataHandler()->getParameter('user')) {
      $memberships = $this->getParDataManager()->hasMemberships($account, TRUE);

      $user_organisations = array_filter($memberships, function ($membership) {
        return ('par_data_organisation' === $membership->getEntityTypeId());
      });
      $user_authorities = array_filter($memberships, function ($membership) {
        return ('par_data_authority' === $membership->getEntityTypeId());
      });
    }
    else if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      // Get the directly related authorities and organisations.
      $par_relationship_manager = $this->getParDataManager()->getReducedIterator(1);
      $memberships = $par_relationship_manager->getRelatedEntities($par_data_person);

      $user_organisations = array_filter($memberships, function ($membership) {
        return ('par_data_organisation' === $membership->getEntityTypeId());
      });
      $user_authorities = array_filter($memberships, function ($membership) {
        return ('par_data_authority' === $membership->getEntityTypeId());
      });
    }

    // Store the existing organisation memberships
    $organisation_ids = [];
    foreach ($user_organisations as $user_organisation) {
      $organisation_ids[] = $user_organisation->id();
    }
    $this->getFlowDataHandler()->setFormPermValue('par_data_organisation_id', $organisation_ids);

    // Store the existing authority memberships.
    $authority_ids = [];
    foreach ($user_authorities as $user_authority) {
      $authority_ids[] = $user_authority->id();
    }
    $this->getFlowDataHandler()->setFormPermValue('par_data_authority_id', $authority_ids);

    // Get the memberships for the current user.
    $current_user = $this->getFlowNegotiator()->getCurrentUser();
    $memberships = $this->getParDataManager()->hasMemberships($current_user, TRUE);

    $organisation_options = array_filter($memberships, function ($membership) {
      return ('par_data_organisation' === $membership->getEntityTypeId());
    });
    $authority_options = array_filter($memberships, function ($membership) {
      return ('par_data_authority' === $membership->getEntityTypeId());
    });

    $this->getFlowDataHandler()->setFormPermValue('organisation_options', $this->getParDataManager()->getEntitiesAsOptions($organisation_options, []));
    $this->getFlowDataHandler()->setFormPermValue('authority_options', $this->getParDataManager()->getEntitiesAsOptions($authority_options, []));

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Get all the allowed organisations and authorities.
    $organisation_options = $this->getFlowDataHandler()->getFormPermValue('organisation_options');
    $authority_options = $this->getFlowDataHandler()->getFormPermValue('authority_options');

    if (!empty($organisation_options)) {
      $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE);
      $default_value = $this->getDefaultValuesByKey("par_data_organisation_id", $cardinality, NULL);
      $form['par_data_organisation_id'] = [
        '#type' => $multiple ? 'checkboxes' : 'radios',
        '#title' => t('Which organisations are they a member of?'),
        '#options' => $organisation_options,
        '#default_value' => $multiple ? (array) $default_value : $default_value,
        '#attributes' => ['class' => ['form-group']],
      ];
    }

    if (!empty($authority_options)) {
      $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE);
      $default_value = $this->getDefaultValuesByKey("par_data_authority_id", $cardinality, NULL);
      $form['par_data_authority_id'] = [
        '#type' => $multiple ? 'checkboxes' : 'radios',
        '#title' => t('Which authorities are they a member of?'),
        '#options' => $authority_options,
        '#default_value' => $multiple ? (array) $default_value : $default_value,
        '#attributes' => ['class' => ['form-group']],
      ];
    }

    if (empty($organisation_options) && empty($authority_options)) {
      $form['intro'] = [
        '#type' => 'markup',
        '#markup' => "There are no memberships to select from.",
        '#prefix' => '<p class=""form-group">',
        '#suffix' => '</p>',
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    // Allow validation to be disabled if memberships are not required.
    $required = $this->getFlowDataHandler()->getDefaultValues('required', TRUE);

    // Get all the allowed organisations and authorities.
    $organisation_options = $this->getFlowDataHandler()->getFormPermValue('organisation_options');
    $authority_options = $this->getFlowDataHandler()->getFormPermValue('authority_options');

    // If multiple choices are allowed the resulting value may be an array with keys but empty values.
    $organisation_element_key = $this->getElementKey('par_data_organisation_id', $cardinality);
    $organisations_selected = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE) ?
      NestedArray::filter((array) $form_state->getValue($organisation_element_key)) :
      $form_state->getValue($organisation_element_key);

    // If multiple choices are allowed the resulting value may be an array with keys but empty values.
    $authority_element_key = $this->getElementKey('par_data_authority_id', $cardinality);
    $authorities_selected = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE) ?
      NestedArray::filter((array) $form_state->getValue($authority_element_key)) :
      $form_state->getValue($authority_element_key);

    // One of either the authorities of the organisations must be selected.
    if ($required
      && (empty($organisation_options) || empty($organisations_selected))
      && (empty($authority_options) || empty($authorities_selected))) {
      $organisation_id_key = $this->getElementKey('par_data_organisation_id', $cardinality, TRUE);
      $authority_id_key = $this->getElementKey('par_data_authority_id', $cardinality, TRUE);

      $form_state->setErrorByName($this->getElementName($organisation_element_key), $this->wrapErrorMessage('You must add this person to at least one organisation or authority.', $this->getElementId($organisation_id_key, $form)));
      $form_state->setErrorByName($this->getElementName($authority_element_key), $this->wrapErrorMessage('You must add this person to at least one organisation or authority.', $this->getElementId($authority_id_key, $form)));
    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
