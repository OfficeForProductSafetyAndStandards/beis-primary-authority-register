<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the sic code details.
 */
class ParPartnershipFlowsSicCodeForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   * @param int $sic_code_delta
   *   The field delta to update.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, $field_sic_code_delta = NULL) {
    if (!is_null($field_sic_code_delta)) {
      // Store the current value of the sic_code if it's being edited.
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      $sic_code = $par_data_organisation ? $par_data_organisation->get('field_sic_code')->referencedEntities()[$field_sic_code_delta] : NULL;
      if ($id = $sic_code->id()) {
        $this->getFlowDataHandler()->setFormPermValue("sic_code", $id);
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, $field_sic_code_delta = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $field_sic_code_delta);
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    // Display the correct introductory text based on the action that is being performed.
    $intro_text = $this->getFlowDataHandler()->getDefaultValues("sic_code", NULL) ?
      'Change the SIC Code of your organisation' :
      'Add a new SIC Code to your organisation';

    $options = [];
    // Get the list of valid sic codes.
    // @TODO This kinda logic shouldn't be in this form. Let's create a method to do this mapping.
    $sic_codes = $this->parDataManager->getEntitiesByType('par_data_sic_code');
    foreach ($sic_codes as $sic_code) {
      $options[$sic_code->id()] = $sic_code->label();
    }

    $form['sic_code'] = [
      '#type' => 'select',
      '#title' => $this->t($intro_text),
      '#options' => $options,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("sic_code"),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the edited value for the organisation's sic code field.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $sic_code_delta = $this->getFlowDataHandler()->getParameter('field_sic_code_delta');

    $items = $par_data_organisation->get('field_sic_code')->getValue();
    if ($par_data_organisation && isset($sic_code_delta)) {
      $items[$sic_code_delta] = $this->getFlowDataHandler()->getTempDataValue('sic_code');
    }
    else {
      $items[] = $this->getFlowDataHandler()->getTempDataValue('sic_code');
    }
    $par_data_organisation->set('field_sic_code', $items);

    if ($par_data_organisation->save()) {
      $this->getFlowDataHandler()->deleteStore();
    } else {
      $message = $this->t('This %field could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getFlowDataHandler()->getTempDataValue('trading_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

  }

}
