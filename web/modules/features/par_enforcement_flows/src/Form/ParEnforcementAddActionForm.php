<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;

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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->retrieveEditableValues();

    $enforcement_notice_bundle = $this->getParDataManager()->getParBundleEntity('par_data_enforcement_notice');
    $reg_function_bundle = $this->getParDataManager()->getParBundleEntity('par_data_regulatory_function');

    $form['action'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['action']['action_heading']  = [
      '#type' => 'markup',
      '#markup' => $this->t('Enforcement action'),
      '#prefix' => '<h3>',
      '#suffix' => '</h3>',
    ];


    $form['action']['text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('If you are proposing more then one enforcement action, you should add these as separate actions using the link below'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // The advice type.
    $form['enforcement_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Enforcing type'),
      '#options' => $enforcement_notice_bundle->getAllowedValues('notice_type'),
      '#default_value' => $this->getDefaultValues('notice_type'),
      '#required' => TRUE,
      '#prefix' => '<div>',
      '#suffix' => '</div></br>',
    ];

    $form['title_of_action'] = [
      '#title' => $this->t('Title of action'),
      '#type' => 'textfield',
      '#default_value' => $this->getDefaultValues('title_of_action'),

    ];

    // The advice type.
    $form['regulatory_functions'] = [
      '#type' => 'radios',
      '#title' => $this->t('Regulatory function to which this relates'),
      '#options' => $reg_function_bundle->getAllowedValues('function_name'),
      '#default_value' => $this->getDefaultValues('function_name'),
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
