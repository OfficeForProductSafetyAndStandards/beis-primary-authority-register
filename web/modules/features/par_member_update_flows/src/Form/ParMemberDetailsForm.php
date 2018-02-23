<?php

namespace Drupal\par_member_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * The form for the partnership details.
 */
class ParMemberDetailsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Member summary';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_coordinated_business->getOrganisation(TRUE));
//    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_id'] = [
      '#type' => 'hidden',
      '#value' => $par_data_partnership->id(),
    ];

    // Get all the entities that will be rendered.
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');

    // Display organisation name.
    $form['organisation_name'] = $this->renderSection('Member organisation name', $par_data_organisation, ['organisation_name' => 'title'], ['edit-field']);

    // Display the member's address
    $form['member_registered_address'] = $this->renderSection('Member organisation address', $par_data_organisation, ['field_premises' => 'summary'], ['edit-entity']);

    // Display the date the membership began.
    $form['membership_date'] = $this->renderSection('Date of membership', $par_data_coordinated_business, ['date_membership_began' => 'default'], ['edit-field']);
    if ($par_data_coordinated_business->isRevoked()) {
      $form['membership_cease_date'] = $this->renderSection('Date membership ceased', $par_data_coordinated_business, ['date_membership_ceased' => 'default'], ['edit-field']);
    }

    // Display the member'sprimary contact details
    $form['member_primary_contact'] = $this->renderSection('Primary contact', $par_data_organisation, ['field_person' => 'summary'], ['edit-entity']);

    // Display contacts at the organisation.
    $form['legal_entities'] = $this->renderSection('Legal entities', $par_data_organisation, ['field_legal_entity' => 'summary'], ['edit-entity', 'add']);

    // Display the trading names.
    $form['trading_names'] = $this->renderSection('Trading names', $par_data_organisation, ['trading_name' => 'full'], ['edit-field', 'add']);

    // Display whether this is covered by an inspeciton plan.
    $form['covered_by_inspection'] = $this->renderSection('Covered by inspection plan', $par_data_coordinated_business, ['covered_by_inspection' => 'default'], ['edit-field']);

    return parent::buildForm($form, $form_state);
  }

}
