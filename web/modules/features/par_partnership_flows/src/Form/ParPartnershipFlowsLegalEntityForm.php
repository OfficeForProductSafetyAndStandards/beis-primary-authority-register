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
  protected $formItems = [
    'par_data_legal_entity:legal_entity' => [
      'registered_name' => 'registered_name',
      'legal_entity_type' => 'legal_entity_type',
      'registered_number' => 'registered_number',
    ]
  ];

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

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
    if ($par_data_legal_entity) {
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_registered_name", $par_data_legal_entity->get('registered_name')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_registered_number", $par_data_legal_entity->get('registered_number')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_legal_entity_type", $par_data_legal_entity->get('legal_entity_type')->getString());
      $this->getFlowDataHandler()->setFormPermValue('legal_entity_id', $par_data_legal_entity->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataLegalEntity $par_data_legal_entity = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_legal_entity);
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    $form['legal_entity_intro_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What is a legal entity?'),
    ];

    $form['legal_entity_intro_fieldset']['intro'] = [
      '#type' => 'markup',
      '#markup' => "<p>" . $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities.") . "</p>",
    ];

    $form['registered_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter name of the legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_name"),
    ];

    $form['legal_entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Select type of Legal Entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_legal_entity_type"),
      '#options' => $legal_entity_bundle->getAllowedValues('legal_entity_type'),
    ];

    $form['registered_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the registration number'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("legal_entity_registered_number"),
      '#states' => [
        'visible' => [
          'select[name="legal_entity_type"]' => [
            ['value' => 'limited_company'],
            ['value' => 'public_limited_company'],
            ['value' => 'limited_liability_partnership'],
            ['value' => 'registered_charity'],
            ['value' => 'partnership'],
            ['value' => 'limited_partnership'],
            ['value' => 'other'],
          ],
        ],
      ],
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
    $legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity');

    // Legal entities that accept registered numbers.
    $registered_number_types = [
      'limited_company',
      'public_limited_company',
      'limited_liability_partnership',
      'registered_charity',
      'partnership',
      'limited_partnership',
      'other',
    ];

    // Nullify registered number if not one of the types specified.
    if ($legal_entity && !in_array($this->getFlowDataHandler()->getTempDataValue('legal_entity_type'), $registered_number_types)) {
      $this->getFlowDataHandler()->setTempDataValue('registered_number', NULL);
    }

    // Edit existing legal entity / add new legal entity.
    if ($legal_entity) {
      $legal_entity->set('registered_name', $this->getFlowDataHandler()->getTempDataValue('registered_name'));
      $legal_entity->set('legal_entity_type', $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'));
      $legal_entity->set('registered_number', $this->getFlowDataHandler()->getTempDataValue('registered_number'));

      if ($legal_entity->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
    else {
      // Create a new legal entity.
      $legal_entity = ParDataLegalEntity::create([
        'type' => 'legal_entity',
        'name' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
        'registered_name' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
        'registered_number' => $this->getFlowDataHandler()->getTempDataValue('registered_number'),
        'legal_entity_type' => $this->getFlowDataHandler()->getTempDataValue('legal_entity_type'),
      ]);
      $legal_entity->save();

      // Now add the legal entity to the partnership.
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_partnership->addLegalEntity($legal_entity);

      // Add the new legal entity to the organisation.
      $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
      $par_data_organisation->addLegalEntity($legal_entity);

      // Commit partnership/organisation changes.
      if ($legal_entity->id() && $par_data_partnership->save() && $par_data_organisation->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %field could not be saved for %form_id');
        $replacements = [
          '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

  }

}
