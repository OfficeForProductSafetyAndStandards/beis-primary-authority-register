<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParConfirmationReviewForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_review';
  }

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Review the partnership summary information below';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_id'] = [
      '#type' => 'hidden',
      '#value' => $par_data_partnership->id(),
    ];

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    // Display details about the organisation for information.
    $form['about_organisation'] = $this->renderSection('About the organisation', $par_data_organisation, ['comments' => 'about']);

    // Display organisation name and organisation primary address.
    $form['organisation_name'] = $this->renderSection('Business name', $par_data_organisation, ['organisation_name' => 'title'], [], TRUE, TRUE);
    $form['organisation_registered_address'] = $this->renderSection('Business address', $par_data_organisation, ['field_premises' => 'summary'], [], TRUE, TRUE);

    // Display contacts at the organisation.
    $form['organisation_contacts'] = $this->renderSection('Contacts at the Organisation', $par_data_partnership, ['field_organisation_person' => 'detailed'], [],  TRUE, TRUE);

    // Display SIC code, number of employees.
    $form['sic_code'] = $this->renderSection('Primary SIC code', $par_data_organisation, ['field_sic_code' => 'detailed'], [], TRUE, TRUE);
    $form['number_employees'] = $this->renderSection('Number of employees at the organisation', $par_data_organisation, ['employees_band' => 'detailed']);

    // Display legal entities.
    $form['legal_entities'] = $this->renderSection('Legal entities', $par_data_organisation, ['field_legal_entity' => 'detailed']);

    $form['partnership_info_agreed_business'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm I have reviewed the information above'),
      '#disabled' => $par_data_partnership->get('partnership_info_agreed_business')->getString(),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("partnership_info_agreed_business"),
      '#return_value' => 'on',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Make sure the confirm box is ticked.
    if (!$form_state->getValue('partnership_info_agreed_business')) {
      $this->setElementError('partnership_info_agreed_business', $form_state, 'Please confirm you have reviewed the details.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = ParDataPartnership::load($this->getFlowDataHandler()->getTempDataValue('partnership_id'));

    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_person = current($par_data_organisation->getPerson());

    if ($par_data_partnership && !$par_data_partnership->getBoolean('partnership_info_agreed_business')) {
      // Save the value for the confirmation field.
      $par_data_partnership->set('partnership_info_agreed_business', $this->decideBooleanValue($this->getFlowDataHandler()->getTempDataValue('partnership_info_agreed_business')));

      // Set partnership status.
      $par_data_partnership->set('partnership_status', 'confirmed_business');
    }

    if ($par_data_partnership->save()) {
      $this->getFlowDataHandler()->deleteStore();

      $route_params = [
        'par_data_partnership' => $par_data_partnership->id(),
        'par_data_person' => $par_data_person->id()
      ];
      $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getNextRoute('save'), $route_params);
    }
    else {
      $message = $this->t('This %confirm could not be saved for %form_id');
      $replacements = [
        '%confirm' => $par_data_partnership->get('partnership_info_agreed_business')->toString(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);

      // If the partnership could not be saved the application can't be progressed.
      // @TODO Find a better way to alert the user without redirecting them away from the form.
      drupal_set_message('There was an error progressing your partnership, please contact the helpdesk for more information.');
      $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getPrevRoute('cancel'));
    }
  }

}
