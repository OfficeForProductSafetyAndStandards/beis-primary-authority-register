<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPerson;

/**
 * The base form controller for all PAR raise enforcement forms.
 */
abstract class ParBaseEnforcementForm extends ParBaseForm {

  /**
   *  Get the cached enforcing authority ID.
   *
   *  @return string
   *    Enforcing authority ID stored in te temp cache.
   */
  public function getEnforcingAuthorityID() {
    return $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');
  }

  /**
   *  Get the cached enforcing officer ID.
   *
   * @return string
   *  Enforcing officer ID stored in te temp cache.
   */
  public function getEnforcingPersonID() {
    return $this->getDefaultValues('enforcement_officer_id', '', 'par_enforcement_officer_details');
  }

  /**
   * Get the cached enforced organisation ID.
   *
   * @return string
   *  Enforced organisation ID stored in te temp cache.
   */
  public function getEnforcedOrganisationID() {
    return $this->getDefaultValues('par_data_organisation_id', '', 'par_enforce_organisation');
  }

  /**
   * Get the cached enforced legal entity ID.
   *
   * @return string
   *    Enforced legal entity ID stored in te temp cache.
   */
  public function getEnforcedLegalEntity() {
    return $this->getDefaultValues('legal_entities_select', '', 'par_enforcement_notice_raise');
  }

  /**
   * Get the cached enforced legal entity registered name.
   *
   *  @return ParDataLegalEntity | string
   *    ParDataLegalEntity entity object or the custom text entered in the form.
   */
  public function getEnforcedLegalEntityName() {

    $selected_entity =  $this->getEnforcedLegalEntity();

    if ($selected_entity == 'add_new') {
      return $this->getDefaultValues('alternative_legal_entity', '', 'par_enforcement_notice_raise');
    }
    else {
      return ParDataLegalEntity::load($selected_entity)->get('registered_name')->getString();
    }
  }

  /**
   *  Get the cached enforcing authority entity.
   *
   *  @return ParDataAuthority
   *    ParDataAuthority entity object
   */
  public function getEnforcingAuthorityEntity() {
    return ParDataAuthority::load($this->getEnforcingAuthorityID());
  }

  /**
   * Get the cached enforced organisation entity.
   *
   * @return ParDataOrganisation
   *  ParDataOrganisation entity object
   */
  public function getEnforcedOrganisationEntity() {
    return ParDataOrganisation::load($this->getEnforcedOrganisationID());
  }

  /**
   * Get the cached enforcing officer entity.
   *
   * @return ParDataPerson
   *  ParDataPerson entity object
   */
  public function getEnforcingOfficerEntity() {
    return ParDataPerson::load($this->getEnforcingPersonID());
  }

  /**
   * Constructs the enforcement form elements shared across the enforcement
   * forms within the raise enforcement flow.
   *
   * @return $form
   *  Render array containing the form elements required for the raise flow.
   *
   */
  public function BuildRaiseEnforcementFormElements() {

    // Load required entities for the enforcement flow.
    $par_data_authority = $this->getEnforcingAuthorityEntity();
    $form['authority'] = $this->renderSection('Enforced by', $par_data_authority, ['authority_name' => 'summary']);

    return $form;
  }

  /**
   *  Helper function to validate all required data has been setup in the temp cache required
   *  for the raise enforcement flow.
   *  This validation prevents system failures in later steps.
   *
   * @return array $form | TRUE
   *  The form array with form elements when validation fails or boolean TRUE if passed.
   */
  public function validateEnforcementCachedData() {

    // Get the correct authority id / organisation id set within the previous form.
    $enforcing_authority_id = $this->getEnforcingAuthorityID();
    $enforced_organisation_id = $this->getEnforcedOrganisationID();
    $enforcement_officer_id = $this->getEnforcingPersonID();

    if (empty($enforcing_authority_id)) {
      $form['authority_enforcement_ids'] = [
        '#type' => 'markup',
        '#markup' => $this->t('You have not selected an authority to enforce on behalf of, please go back to the previous steps to complete this form.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong><p>',
      ];
    }

    if (empty($enforcement_officer_id)) {
      $form['enforcement_ids'] = [
        '#type' => 'markup',
        '#markup' => $this->t('There is no valid enforcement officer data in the system please contact the help-desk.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong><p>',
      ];
    }

    if (empty($enforced_organisation_id)) {
      $form['organisation_enforcement_ids'] = [
        '#type' => 'markup',
        '#markup' => $this->t('You have not selected an organisation to enforce on behalf of, please go back to the previous steps to complete this form.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong><p>',
      ];
    }

    // Defensive coding don't attempt to render form elements without having the appropriate object
    // $par_data_organisation and $par_data_authority proceeding without them will cause system failures.
    if (empty($enforced_organisation_id) || empty($enforcing_authority_id)) {
      return $form;
    }
    return TRUE;
  }

  /**
   *  Get the enforcing officer ParDataPerson object.
   *
   * @return ParDataPerson object | FALSE
   */
  public function getEnforcingPerson() {

    if ($par_data_authority = $this->getEnforcingAuthorityEntity()) {
      // Get logged in user ParDataPerson(s) related to the primary authority.
      return $this->getParDataManager()->getUserPerson($this->getCurrentUser(), $par_data_authority);
    }
    return FALSE;
  }

  /**
   *  Get the legal entities of the enforced organisation.
   */
  public function getEnforcedOrganisationLegalEntities() {
    return $this->getEnforcedOrganisationEntity()->getPartnershipLegalEntities();
  }

  /**
   *  Helper function for generating the enforcement flow page title markup.
   *
   * * @return string  | FALSE
   *  The default title to use on the current form or FALSE which will fall back to the
   *  flow defaults.
   */
  public function RaiseEnforcementTitleCallback() {

    $par_data_partnership = $this->getRouteParam('par_data_partnership');

    if ($par_data_partnership) {
      $this->setState("edit:{$par_data_partnership->id()}");
      $enforced_entity_name = $this->getEnforcedEntityName();

      if ($enforced_entity_name) {
        return  'Proposed enforcement notification regarding | '. $enforced_entity_name;
      }
    }
    return FALSE;
  }



  /**
   *  Helper function to get either the legal entity name or organisation depending on whats available.
   *
   * @return string  | FALSE
   *  The name of the enforced entity or organisation or False if we don't have one.
   */
  function getEnforcedEntityName() {

    // Depending on the form in this process we may not have a legal entity yet.
    if ($this->getEnforcedLegalEntity()) {
      return $this->getEnforcedLegalEntityName();
    }
    elseif ($this->getEnforcedOrganisationEntity()) {
      return $this->getEnforcedOrganisationEntity()->get('organisation_name')->getString();
    }
    else {
      return FALSE;
    }
  }
}
