<?php

namespace Drupal\par_flow_transition_business\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionLegalEntityForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_business';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_business_legal_entity';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataLegalEntity $par_data_legal_entity
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataLegalEntity $par_data_legal_entity = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

    if ($par_data_legal_entity) {
      // Contact.
      $this->loadDataValue("legal_entity_{$par_data_legal_entity->id()}_registered_name", $par_data_legal_entity->get('registered_name')->getString());
      $this->loadDataValue("legal_entity_{$par_data_legal_entity->id()}_registered_number", $par_data_legal_entity->get('registered_number')->getString());
      $this->loadDataValue("legal_entity_{$par_data_legal_entity->id()}_legal_entity_type", $par_data_legal_entity->get('legal_entity_type')->getString());
      $this->loadDataValue('legal_entity_id', $par_data_legal_entity->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataLegalEntity $par_data_legal_entity = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_legal_entity);

    if (!empty($par_data_legal_entity)) {
      $id = $par_data_legal_entity->id();
      $form['intro'] = [
        '#markup' => $this->t('Change the legal entity of your business'),
      ];
    }
    else {
      $form['intro'] = [
        '#markup' => $this->t('Add a legal entity for your business'),
      ];
    }

    $form['registered_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of legal entity'),
      '#default_value' => isset($id) ? $this->getDefaultValues("legal_entity_{$id}_registered_name") : '',
      '#description' => $this->t('A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisation such as trusts and charities.'),
      '#required' => TRUE,
    ];

    // Legal Type.
    // @todo Need to put the correct list here.
    $title_options = [
      'Ms',
      'Fictional Company',
      'Mr',
      'Dr',
    ];
    $form['Legal_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type of Legal Entity'),
      '#default_value' => isset($id) ? $this->getDefaultValues("legal_entity_{$id}_legal_entity_type") : '',
      '#options' => array_combine($title_options, $title_options),
      '#required' => TRUE,
    ];

    // The Person's name.
    $form['company_house_no'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Companies House Number'),
      '#default_value' => isset($id) ? $this->getDefaultValues("legal_entity_{$id}_registered_number") : '',
      '#required' => TRUE,
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];

    $previous_link = $this->getFlow()->getLinkByStep(4)->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#prefix' => '<br>',
      '#markup' => t('@link', ['@link' => $previous_link]),
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

    // Save the value for the about_partnership field.
    $legal_entity = $this->getRouteParam('par_data_legal_entity');
    if (!empty($legal_entity)) {
      $legal_entity->set('registered_name', $this->getTempDataValue('registered_name'));
      $legal_entity->set('legal_entity_type', $this->getTempDataValue('Legal_entity_type'));
      $legal_entity->set('registered_number', $this->getTempDataValue('company_house_no'));

      if ($legal_entity->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
    else {
      // Adding a new legal entity.
      $legal_entity = ParDataLegalEntity::create([
        'type' => 'legal_entity',
        'name' => $this->getTempDataValue('registered_name'),
        'uid' => 1,
        'registered_name' => $this->getTempDataValue('registered_name'),
        'registered_number' => $this->getTempDataValue('company_house_no'),
        'legal_entity_type' => $this->getTempDataValue('Legal_entity_type'),
      ]);
      $legal_entity->save();

      // Now add the legal entity to the organisation.
      $par_data_partnership = $this->getRouteParam('par_data_partnership');
      $par_data_organisation = current($par_data_partnership->get('organisation')->referencedEntities());
      $legal_entities = $par_data_organisation->get('legal_entity')->referencedEntities();

      $legal_entities[] = $legal_entity;
      $par_data_organisation->set('legal_entity', $legal_entities);

      if ($par_data_organisation->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(4), $this->getRouteParams());
  }

}
