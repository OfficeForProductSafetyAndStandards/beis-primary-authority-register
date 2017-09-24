<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParEnforcementRaiseNoticeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_raise';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $this->retrieveEditableValues();
    $enforcement_notice_bundle = $this->getParDataManager()->getParBundleEntity('par_data_enforcement_notice');

    //get the correct par_data_authority_id set by the previous form
    $authority_id = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_authority = current($par_data_partnership->getAuthority());

    $form['authority'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['authority']['authority_heading']  = [
      '#type' => 'markup',
      '#markup' => $this->t('Notification of Enforcement action'),
    ];


    $form['authority']['authority_name'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<div><h1>',
      '#suffix' => '</h1></div>',
    ];

    $form['organisation'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];


    $form['organisation']['organisation_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Regarding'),

    ];

    $form['organisation']['organisation_name'] = [
      '#type' => 'markup',
      '#markup' => $par_data_organisation->get('organisation_name')->getString(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    // Display the primary address.
    $form['registered_address'] = $this->renderSection('Registered address', $par_data_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);

    $form['enforcement_title'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Include the following information'),
    ];

    $form['enforcement_text'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#prefix' => '<ul>',
      '#suffix' => '</ul>',
    ];

    $enforcement_data = array('Full details of the contravention', 'Which products or services are affected', 'Your proposed text for any statutory notice or draft changes etc', 'Your reasons for proposing the enforcement action');

    foreach ($enforcement_data as $key => $value) {

      $form['enforcement_text']['body'][$key] = [
        '#type' => 'markup',
        '#markup' => $this->t($value),
        '#prefix' => '<li>',
        '#suffix' => '</li>',
      ];
    }

    $legal_entity_reg_names = $par_data_organisation->getPartnershipLegalEntities();
    //After getting a list of all the associated legal entities add a use custom option.
    $legal_entity_reg_names['add_new']  = 'Add a legal entity';

      $form['legal_entities_select'] = [
        '#type' => 'radios',
        '#title' => $this->t('Select a legal entity'),
        '#options' => $legal_entity_reg_names,
        '#default_value' => $this->getDefaultValues('legal_entities_select'),
        '#required' => TRUE,
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];

    $form['alternative_legal_entity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alternative legal entity'),
      '#default_value' => $this->getDefaultValues("alternative_legal_entity"),
      '#states' => array(
        'visible' => array(
          ':input[name="legal_entities_select"]' => array('value' => 'add_new'),
        )
      ),
    ];

    $form['action_summmary'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your summary of enforcement action(s)'),
      '#default_value' => $this->getDefaultValues("action_summmary"),
     ];

    /*$form['action_heading']  = [
      '#type' => 'markup',
      '#markup' => $this->t('Enforcement action(s)'),
      '#prefix' => '<h3>',
      '#suffix' => '</h3>',
    ];

     $form['action_add'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#description' => $this->t('If you are proposing more then one enforcement action, you should add these as separate actions using the link below'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

   $add_action_link = $this->getFlow()->getNextLink()->setText('Add an enforcement action')->toString();
    $form['action_add']['add_link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $add_action_link]),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];*/

    $form['enforcement_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Enforcing type'),
      '#options' => $enforcement_notice_bundle->getAllowedValues('notice_type'),
      '#default_value' => $this->getDefaultValues('enforcement_type'),
      '#required' => TRUE,
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];
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

    $partnership = $this->getRouteParam('par_data_partnership');

    $enforcementNotice_data = [];

    $enforcementNotice_data = [
      'type' => 'enforcement_notice',
      'notice_type' => $this->getTempDataValue('enforcement_type'),
      'summary' => $this->getTempDataValue('action_summmary'),
      'field_regulatory_function' => $this->getTempDataValue('regulatory_functions'),
    ];

    $legal_entity_value = $this->getTempDataValue('legal_entities_select');

    if ($legal_entity_value == 'add_new') {
      $enforcementNotice_data['legal_entity_name'] = $this->getTempDataValue('alternative_legal_entity');
    } else {
      //we are dealing with an entity id and storing to an entity ref field.
      $enforcementNotice_data['field_legal_entity'] = $legal_entity_value;
    }

    $enforcementAction = \Drupal::entityManager()->getStorage('par_data_enforcement_notice')->create($enforcementNotice_data);

    if ($enforcementAction->save()) {
      $this->deleteStore();
      $form_state->setRedirect($this->getFlow()->getNextRoute('next'), ['par_data_partnership' => $partnership->id(), 'par_data_enforcement_notice' =>$enforcementAction->id()]);
    }
    else {
      $message = $this->t('The enforcement entity %entity_id could not be saved for %form_id');
      $replacements = [
        '%entity_id' => $enforcementAction->id(),
        '%form_id' => $this->getFormId(),
     ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
