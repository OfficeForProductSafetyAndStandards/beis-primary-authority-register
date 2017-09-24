<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsApplicationConfirmationForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_authority_details';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    if ($par_data_partnership) {
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      return $par_data_organisation->get('organisation_name')->getString();
    }

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      $this->loadDataValue("partnership_info_agreed_authority", $par_data_partnership->get('partnership_info_agreed_authority')->getString());
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    // Display the primary address along with the link to edit it.
    $form['registered_address'] = $this->renderSection('Registered address', $par_data_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);

    // View and perform operations on the information about the business.
    $form['about_business'] = $this->renderSection('About the business', $par_data_organisation, ['comments' => 'about']);

    // Everything below is for the authorioty to edit and add to.
    $par_data_authority = current($par_data_partnership->getAuthority());
    $form['authority'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    // Display details about the partnership for information.
    $form['about_partnership'] = $this->renderSection('About the partnership', $par_data_partnership, ['about_partnership' => 'about']);

    // Display the authority contacts for information.
    $form['authority_contacts'] = $this->renderSection('Contacts - Primary Authority', $par_data_partnership, ['field_authority_person' => 'detailed']);

    // Display all the legal entities along with the links for the allowed
    // operations on these.
    $form['organisation_contacts'] = $this->renderSection('Contacts - Organisation', $par_data_partnership, ['field_organisation_person' => 'detailed']);

    $form['partnership_info_agreed_authority'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm I have reviewed the partnership summary information above'),
      '#disabled' => $par_data_partnership->get('partnership_info_agreed_authority')->getString(),
      '#default_value' => $this->getDefaultValues("partnership_info_agreed_authority"),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Make sure the confirm box is ticked.
    if (!$form_state->getValue('partnership_info_agreed_authority')) {
      $this->setElementError('partnership_info_agreed_authority', $form_state, 'Please confirm you have reviewed the details.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getRouteParam('par_data_partnership');
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_person = current($par_data_organisation->getPerson());

    if ($par_data_partnership && !$par_data_partnership->get('partnership_info_agreed_authority')->getString()) {

      // Save the value for the confirmation field.
      $par_data_partnership->set('partnership_info_agreed_authority', $this->getTempDataValue('partnership_info_agreed_authority'));

      if ($par_data_partnership->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('This %confirm could not be saved for %form_id');
        $replacements = [
          '%confirm' => $par_data_partnership->get('partnership_info_agreed_authority')->toString(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }

    $form_state->setRedirect($this->getFlow()->getNextRoute('save'), ['par_data_partnership' => $par_data_partnership->id(), 'par_data_person' => $par_data_person->id()]);
  }

}
