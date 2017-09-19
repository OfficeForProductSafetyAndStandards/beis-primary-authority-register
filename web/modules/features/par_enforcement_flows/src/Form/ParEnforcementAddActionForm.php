<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParEnforcementAddActionForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_add_action';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues();

    $reg_function_bundle = $this->getParDataManager()->getParBundleEntity('par_data_regulatory_function');
    $reg_functions_entities = $par_data_partnership->getRegulatoryFunction();
    $reg_function_names = array();

    foreach ($reg_functions_entities as $key => $current_reg_function_obj) {
      $reg_function_names[] =  $current_reg_function_obj->get('function_name')->getString();
    }

    $form['title_of_action'] = [
      '#title' => $this->t('Title of action'),
      '#type' => 'textfield',
      '#default_value' => $this->getDefaultValues('title_of_action'),
    ];

    $form['regulatory_functions'] = [
      '#type' => 'radios',
      '#title' => $this->t('Regulatory function to which this relates'),
      '#options' => $reg_function_names,
      '#default_value' => $this->getDefaultValues('regulatory_functions'),
      '#required' => TRUE,
    ];

    $form['details'] = [
      '#title' => $this->t('Details'),
      '#type' => 'textarea',
      '#default_value' => $this->getDefaultValues('details'),
    ];

    $add_file_upload_link = $this->getFlow()->getNextLink('file_upload')->setText('Attach file')->toString();
    $form['add_upload_files'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $add_file_upload_link]) . ' (document, photo or video)',
      '#prefix' => '<div>',
      '#suffix' => '</div></br>',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#name' => 'next',
      '#value' => $this->t('Continue'),
    ];

    $cancel_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $cancel_link]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
