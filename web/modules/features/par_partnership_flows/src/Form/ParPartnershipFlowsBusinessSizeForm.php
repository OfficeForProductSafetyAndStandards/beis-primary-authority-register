<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsBusinessSizeForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_business_size';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // If we want to use values already saved we have to tell
      // the form about them.
      $par_data_organisation = current($par_data_partnership->getOrganisation());

      $this->loadDataValue('business_size', $par_data_organisation->get('size')->getString());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    $organisation_bundle = $this->getParDataManager()->getParBundleEntity('par_data_organisation');

    $form['info'] = [
      '#markup' => t('Enter the number of members in your membership list'),
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    // Business details.
    $form['business_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Number of members'),
      '#default_value' => $this->getDefaultValues('business_size'),
      '#options' => $organisation_bundle->getAllowedValues('size'),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $partnership = $this->getRouteParam('par_data_partnership');
    $par_data_organisation = current($partnership->getOrganisation());
    $par_data_organisation->set('size', $this->getTempDataValue('business_size'));
    if ($par_data_organisation->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('The %field field could not be saved for %form_id');
      $replacements = [
        '%field' => 'size',
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

  }

}
