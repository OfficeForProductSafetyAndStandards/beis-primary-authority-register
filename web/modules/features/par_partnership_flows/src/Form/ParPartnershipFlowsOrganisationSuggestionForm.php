<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The de-duping form.
 */
class ParPartnershipFlowsOrganisationSuggestionForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  protected $pageTitle = 'Are you looking for one of these businesses?';

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_organisation');
    $searchQuery = $this->getFlowDataHandler()->getDefaultValues('organisation_name', '', $cid);

    // Go to previous step if search query is not specified.
    if (!$searchQuery) {
      // @TODO Find a way to notify the user they have been redirected.
      return $this->redirect($this->getFlowNegotiator()->getFlow()->progressRoute('prev'), $this->getRouteParams());
    }

    $conditions = [
      'name' => [
        'OR' => [
          ['organisation_name', $searchQuery, 'STARTS_WITH'],
          ['trading_name', $searchQuery, 'STARTS_WITH'],
        ]
      ],
    ];

    $organisations = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_organisation', $conditions, 10);

    $radio_options = [];

    foreach ($organisations as $organisation) {
      // PAR-1172 Do not display organisations in coordinated partnerships.
      if (!$organisation->isCoordinatedMember()) {
        $label = $this->renderSection('Organisation', $organisation, [
          'organisation_name' => 'summary',
          'field_premises' => 'summary',
        ], ['edit-entity'], FALSE, TRUE);
        $radio_options[$organisation->id()] = render($label);
      }
    }

    // If no suggestions were found we want to automatically submit the form.
    if (count($radio_options) <= 0) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', 'new');
      $this->submitForm($form, $form_state);
      return $this->redirect($this->getFlowNegotiator()->getFlow()->getNextRoute(), $this->getRouteParams());
    }

    $form['par_data_organisation_id'] = [
      '#type' => 'radios',
      '#title' => t('Choose an existing organisation or create a new organisation'),
      '#options' => $radio_options + ['new' => "no, the organisation is not currently in a partnership with any other primary authority"],
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', 'new'),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($organisations);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // If an existing organisation was selected and has an address
    // and contact, skip to the review step, or skip to the contact
    // step if an existing organisation was selected which has an
    // address but no contact.
    $organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', NULL);
    if (isset($organisation_id) && $organisation_id !== 'new') {
      $par_data_organisation = ParDataOrganisation::load($organisation_id);
    }
    if (isset($par_data_organisation)) {
      if (!$par_data_organisation->get('field_person')->isEmpty()) {
        $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getNextRoute('review'), $this->getRouteParams());
      }
      elseif ($par_data_organisation->get('field_person')->isEmpty()
        && !$par_data_organisation->get('field_premises')->isEmpty()) {
        $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getNextRoute('add_contact'), $this->getRouteParams());
      }
    }
  }

}
