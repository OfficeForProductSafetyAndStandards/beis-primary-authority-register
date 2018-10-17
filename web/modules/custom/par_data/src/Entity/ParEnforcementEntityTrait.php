<?php

namespace Drupal\par_data\Entity;

trait ParEnforcementEntityTrait {

  /**
   * Get the Partnership for this Deviation Request.
   *
   * @param boolean $single
   *
   * @return ParDataPartnership|ParDataPartnership[]
   *
   */
  public function getPartnership($single = FALSE) {
    $partnerships = $this->hasField('field_partnership') && !$this->get('field_partnership')->isEmpty() ?
      $this->get('field_partnership')->referencedEntities() : NULL;
    $partnership = $partnerships ? current($partnerships) : NULL;

    return $single ? $partnership : $partnerships;
  }

  /**
   * Get the primary authority for this Deviation Request.
   *
   * @param boolean $single
   *
   * @return ParDataAuthority|ParDataAuthority[]
   *
   */
  public function getPrimaryAuthority($single = FALSE) {
    // Get the authority from the authority field if it exists.
    // Otherwise use the partnership's authority if it exists.
    if ($this->hasField('field_primary_authority') && !$this->get('field_primary_authority')->isEmpty()) {
      $authorities = $this->get('field_primary_authority')->referencedEntities();
      $authority = $authorities ? current($authorities) : NULL;

      return $single ? $authority : $authorities;
    }
    elseif ($partnership = $this->getPartnership(TRUE)){
      return $partnership->getAuthority($single);
    }

    return NULL;
  }

  /**
   * Get the enforcing authority for this Deviation Request.
   *
   * @param boolean $single
   *
   * @return ParDataAuthority|ParDataAuthority[]
   */
  public function getEnforcingAuthority($single = FALSE) {
    $authorities = $this->hasField('field_enforcing_authority') && !$this->get('field_enforcing_authority')->isEmpty() ?
      $this->get('field_enforcing_authority')->referencedEntities() : NULL;
    $authority = $authorities ? current($authorities) : NULL;

    return $single ? $authority : $authorities;
  }

  /**
   * Get the enforced organisation for this Enforcement Notice.
   *
   * @param boolean $single
   *
   * @return ParDataOrganisation|ParDataOrganisation[]
   */
  public function getEnforcedOrganisation($single = FALSE) {
    // Get the organisation from the organisation field if it exists.
    // Otherwise use the partnership's organisation if it exists and
    // is a direct partnership.
    if ($this->hasField('field_organisation') && !$this->get('field_organisation')->isEmpty()) {
      $organisations = $this->get('field_organisation')->referencedEntities();

      return $single ? current($organisations) : $organisations;
    }
    elseif ($partnership = $this->getPartnership(TRUE)){
      return $partnership->isDirect() ? $partnership->getOrganisation($single) : NULL;
    }

    return NULL;
  }

  /**
   * Get the primary authority contacts.
   *
   * If there is a partnership this will be the primary contact for the partnership.
   * Otherwise it will be the primary contact for the authority as a whole.
   *
   * @param bool $single
   *   Whether to only return the primary contact, or all contacts.
   *
   * @return ParDataPerson|ParDataPerson[]
   *
   */
  public function getPrimaryAuthorityContacts($single = FALSE) {
    if ($partnership = $this->getPartnership(TRUE)) {
      $pa_contact = $partnership->getAuthorityPeople($single);
    }
    elseif ($authority = $this->getPrimaryAuthority(TRUE)) {
      $pa_contact = $authority->getPerson($single);
    }

    return isset($pa_contact) ? $pa_contact : NULL;
  }

  /**
   * Get the combined primary authority contacts for the partnership and authority.
   *
   * @return ParDataPerson[]
   *
   */
  public function getAllPrimaryAuthorityContacts() {
    if ($partnership = $this->getPartnership(TRUE)) {
      $partnership_contact = (array) $partnership->getAuthorityPeople();
    }
    else {
      $partnership_contact = [];
    }

    if ($authority = $this->getPrimaryAuthority(TRUE)) {
      $authority_contacts = (array) $authority->getPerson();
    }
    else {
      $authority_contacts = [];
    }

    $pa_contacts = $this->combineContacts($partnership_contact, $authority_contacts);

    return !empty($pa_contacts) ? $pa_contacts : NULL;
  }

  /**
   * Get the enforcing authority for this Deviation Request.
   *
   * @param bool $single
   *   Whether to only return the primary contact, or all contacts.
   *
   * @return ParDataPerson|ParDataPerson[]
   */
  public function getEnforcingAuthorityContacts($single = FALSE) {
    if ($authority = $this->getEnforcingAuthority(TRUE)) {
      $pa_contact = $authority->getPerson($single);
    }

    return isset($pa_contact) ? $pa_contact : NULL;
  }

  /**
   * Get the primary authority contacts for this notice.
   *
   * If there is a partnership this will be the primary contact for the partnership.
   * Otherwise it will be the primary contact for the authority as a whole.
   *
   * @param bool $single
   *   Whether to only return the primary contact, or all contacts.
   *
   * @return ParDataPerson|ParDataPerson[]
   *
   */
  public function getOrganisationContacts($single = FALSE) {
    if ($partnership = $this->getPartnership(TRUE)) {
      $org_contact = $partnership->getOrganisationPeople($single);
    }
    elseif ($organisation = $this->getEnforcedOrganisation(TRUE)) {
      $org_contact = $organisation->getPerson($single);
    }

    return isset($org_contact) ? $org_contact : NULL;
  }

  /**
   * Get the enforcing officer contact.
   *
   * @param bool $single
   *   Whether to only return the primary contact, or all contacts.
   *
   * @return ParDataPerson|ParDataPerson[]
   */
  public function getEnforcingPerson($single = FALSE) {
    $people = $this->hasField('field_person') && !$this->get('field_person')->isEmpty() ?
      $this->get('field_person')->referencedEntities() : NULL;
    $person = $people ? current($people): NULL;

    return $single ? $person : $people;
  }

  /**
   * Combine array values that may or may not have string keys.
   *
   * @param $first array
   * @param $second array
   *
   * @return array
   */
  public function combineContacts($first, $second) {
    return array_filter(array_merge(array_values($first), array_values($second)));
  }

}
