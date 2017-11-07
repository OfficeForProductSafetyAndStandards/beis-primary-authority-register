<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsLegalEntityForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_legal';
  }

  /**
   * {@inheritdoc}
   */
  protected $formItems = [
    'par_data_legal_entity:legal_entity' => [
      'registered_name' => 'registered_name',
      'legal_entity_type' => 'legal_entity_type',
      'registered_number' => 'company_house_no',
    ]
  ];

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $legal_entity = $this->getRouteParam('par_data_legal_entity');

    $form_context = $legal_entity ? 'Change the legal entity for your organisation' : 'Add a legal entity for your organisation';

    $this->pageTitle = "Update Partnership Information | {$form_context}";

    return parent::titleCallback();
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
      $this->loadDataValue("legal_entity_registered_name", $par_data_legal_entity->get('registered_name')->getString());
      $this->loadDataValue("legal_entity_registered_number", $par_data_legal_entity->get('registered_number')->getString());
      $this->loadDataValue("legal_entity_legal_entity_type", $par_data_legal_entity->get('legal_entity_type')->getString());
      $this->loadDataValue('legal_entity_id', $par_data_legal_entity->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataLegalEntity $par_data_legal_entity = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_legal_entity);
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    $form['intro'] = [
      '#markup' => $this->t('Change the legal entity for your organisation'),
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    $form['registered_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of legal entity'),
      '#default_value' => $this->getDefaultValues("legal_entity_registered_name"),
      '#description' => $this->t('A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisation such as trusts and charities.'),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type of Legal Entity'),
      '#default_value' => $this->getDefaultValues("legal_entity_legal_entity_type"),
      '#options' => $legal_entity_bundle->getAllowedValues('legal_entity_type'),
    ];

    $form['company_house_no'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Companies House Number'),
      '#default_value' => $this->getDefaultValues("legal_entity_registered_number"),
      '#states' => array(
        'visible' => array(
          'select[name="legal_entity_type"]' => array('value' => 'limited_company'),
        ),
      ),
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

    // Only legal entity type "limited_company" saves a companies house number.
    if ($legal_entity && $this->getTempDataValue('legal_entity_type') !== 'limited_company') {
      $this->setTempDataValue('company_house_no', NULL);
    }

    if (!empty($legal_entity)) {
      $legal_entity->set('registered_name', $this->getTempDataValue('registered_name'));
      $legal_entity->set('legal_entity_type', $this->getTempDataValue('legal_entity_type'));
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
        'legal_entity_type' => $this->getTempDataValue('legal_entity_type'),
      ]);
      $legal_entity->save();

      // Now add the legal entity to the organisation.
      $par_data_partnership = $this->getRouteParam('par_data_partnership');
      $par_data_organisation = current($par_data_partnership->getOrganisation());
      $par_data_organisation->addLegalEntity($legal_entity);

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

  }

}
