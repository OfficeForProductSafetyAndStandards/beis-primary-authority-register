<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\registered_organisations\DataException;
use Drupal\registered_organisations\TemporaryException;

/**
 * Legal Entity form plugin.
 *
 * @ParForm(
 *   id = "legal_entity",
 *   title = @Translation("Legal entity form.")
 * )
 */
class ParLegalEntityForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['registry', 'par_data_legal_entity', 'registry', NULL, NULL, 0, [
      'This value should not be null.' => 'You must choose the type of legal entity.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  protected $wrapperName = 'legal entity';

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity')) {
      $this->getFlowDataHandler()->setFormPermValue("registered_name", $par_data_legal_entity->get('registered_name')->getString());
      $this->getFlowDataHandler()->setFormPermValue("legal_entity_type", $par_data_legal_entity->get('legal_entity_type')->getString());
      $this->getFlowDataHandler()->setFormPermValue("registered_number", $par_data_legal_entity->get('registered_number')->getString());
    }

    if ($par_data_partnership = $this->getflowDataHandler()->getParameter('par_data_partnership')) {
      $this->getFlowDataHandler()->setFormPermValue('coordinated_partnership', $par_data_partnership->isCoordinated());
    }

    parent::loadData();
  }

  /**
   * Get the registered organisations manager.
   */
  public function getOrganisationManager() {
    return \Drupal::service('registered_organisations.organisation_manager');
  }

  /**
   * @defaults
   */
  protected $formDefaults = [
    'legal_entity_type' => 'none',
  ];

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    if ($cardinality === 1) {
      $form['legal_entity_intro_fieldset'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('What is a legal entity?'),
        'intro' => [
          '#type' => 'markup',
          '#markup' => "<p>" . $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities.") . "</p>",
        ],
      ];
    }

    if ($this->getFlowDataHandler()->getFormPermValue('coordinated_partnership')) {
      $form['legal_entity_intro_fieldset']['note'] = [
        '#type' => 'markup',
        '#markup' => '<div class="form-group notice">
              <i class="icon icon-important"><span class="visually-hidden">Warning</span></i>
              <strong class="bold-small">Please enter the legal entities for the members covered by this partnership not the co-ordinator.</strong>
            </div>',
      ];
    }

    $registry_options = [
      'companies_house' => 'A registered organisation',
      'charity_commission' => 'A charity',
      ParDataLegalEntity::DEFAULT_REGISTER => 'An unregistered entity',
    ];
    $registry_options_descriptions = [
      'companies_house' => 'Please choose this option if the organisation or partnership is registered with Companies House.',
      'charity_commission' => 'Please choose this option if the charity is registered with the Charity Commission but isn\'t a registered company.',
      ParDataLegalEntity::DEFAULT_REGISTER => 'Please choose this option for sole traders and all other legal entity types.',
    ];

//    if ($cardinality === 1) {
      $form['registry'] = [
        '#type' => 'radios',
        '#title' => 'What type of legal entity is this?',
        '#options' => $registry_options,
        '#options_descriptions' => $registry_options_descriptions,
        '#default_value' => $this->getDefaultValuesByKey('registered_name', $cardinality, ),
        '#after_build' => [
          [get_class($this), 'optionsDescriptions'],
        ],
        '#attributes' => [
          'class' => ['form-group'],
        ],
      ];

      // Follow-up inputs for registered entities.
      $form['registered'] = [
        '#type' => 'container',
        '#states' => [
          'visible' => [
            'input[name="' . $this->getTargetName($this->getElementKey('registry', $cardinality)) . '"]' => [
              ['value' => 'companies_house'],
              ['value' => 'charity_commission'],
            ],
          ],
        ],
        '#attributes' => [
          'class' => ['form-group', 'govuk-radios__conditional'],
        ],
      ];
      $form['registered']['number'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Provide the registration number'),
        '#default_value' => $this->getDefaultValuesByKey('registered_number', $cardinality),
      ];

      // Follow-up inputs for unregistered entities.
      $form['unregistered'] = [
        '#type' => 'container',
        '#states' => [
          'visible' => [
            'input[name="' . $this->getTargetName($this->getElementKey('registry', $cardinality)) . '"]' => [
              ['value' => ParDataLegalEntity::DEFAULT_REGISTER],
            ],
          ],
        ],
        '#attributes' => [
          'class' => ['form-group', 'govuk-radios__conditional'],
        ],
      ];
      $unregistered_type_options = [
        'partnership' => 'Partnership',
        'sole_trader' => 'Sole trader',
        'unincorporated_association' => 'Unincorporated association',
        'other' => 'Other',
      ];
      $unregistered_type_options_descriptions = [
        'partnership' => 'A partnership is a contractual arrangement between two or more people that is set up with a view to profit and to share the profits amongst the partners',
        'sole_trader' => 'A sole trader is an individual who is registered with HMRC for tax purposes',
        'unincorporated_association' => 'A simple way for a group of volunteers to run an organisation for a common purpose',
      ];
      $form['unregistered']['legal_entity_type'] = [
        '#type' => 'radios',
        '#title' => $this->t('How is this entity structured?'),
        '#default_value' => $this->getDefaultValuesByKey('legal_entity_type', $cardinality),
        '#options' => $unregistered_type_options,
        '#options_descriptions' => $unregistered_type_options_descriptions,
        '#after_build' => [
          [get_class($this), 'optionsDescriptions'],
        ],
        '#attributes' => [
          'class' => ['govuk-radios--small', 'form-group'],
        ],
      ];

      $form['unregistered']['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Enter name of the legal entity'),
        '#default_value' => $this->getDefaultValuesByKey('registered_name', $cardinality),
        '#attributes' => [
          'class' => ['form-group'],
        ],
      ];
//    }

    $legal_entity_label = $this->getCardinality() !== 1 ?
      $this->formatPlural($cardinality, 'Legal Entity @index', 'Legal Entity @index (Optional)', ['@index' => $cardinality]) :
      $this->t('Legal Entity');

//    $form['legal_entity'] = [
//      '#type' => 'fieldset',
//      '#title' => $legal_entity_label,
//    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $registry_key = $this->getElementKey('registry', $cardinality);
    $register_id = $form_state->getValue($registry_key);

    if ($register_id === ParDataLegalEntity::DEFAULT_REGISTER) {
      $type_key = $this->getElementKey('legal_entity_type', $cardinality);
      if ($form_state->isValueEmpty($type_key)) {
//        $type_error_key = $this->getElementKey('legal_entity_type', $cardinality);
        $message = $this->wrapErrorMessage('You must choose which legal entity type you are adding.', $this->getElementId($type_key, $form));
        $form_state->setErrorByName($this->getElementName($type_key), $message);
      }

      $name_key = $this->getElementKey('name', $cardinality);
      if ($form_state->isValueEmpty($name_key)) {
//        $name_error_key = $this->getElementKey('name', $cardinality);
        $message = $this->wrapErrorMessage('Please enter a name for this legal entity.', $this->getElementId($name_key, $form));
        $form_state->setErrorByName($this->getElementName($name_key), $message);
      }

      // Validate additional rules.
      parent::validate($form, $form_state, $cardinality, $action);
    }
    else if (in_array($register_id, ['companies_house', 'charity_commission'])) {
      $registered_number_key = $this->getElementKey('number', $cardinality);

      $legal_entity_number = trim((string) $form_state->getValue('number'));
      var_dump(array_keys($form_state->getValues()), $legal_entity_number);
      if (empty($legal_entity_number)) {
        // If no legal entity number has been submitted.
        $number_id_key = $this->getElementKey('number', $cardinality, TRUE);
        $message = $this->wrapErrorMessage('Please enter the legal entity number.', $this->getElementId($number_id_key, $form));
        $form_state->setErrorByName($this->getElementName($registered_number_key), $message);
      }
      else {
        $legal_entity_number = trim($form_state->getValue($registered_number_key));
      }

      try {
        $registry = $this->getOrganisationManager()->getRegistry($register_id);

        if (!empty($legal_entity_number) && !$registry->isValidId($legal_entity_number)) {
          // The legal entity number is in an invalid format.
          $number_key = $this->getElementKey('number', $cardinality, TRUE);
          $message = $this->wrapErrorMessage('The legal entity number you entered is not valid.', $this->getElementId($number_key, $form));
          $form_state->setErrorByName($this->getElementName($number_key), $message);
        }
      }
      catch (PluginNotFoundException $ignore) {
        // Ignore system errors.
      }

//      try {
//        $profile = !empty($legal_entity_number) ?
//          $this->getOrganisationManager()->lookupOrganisation($legal_entity_number) :
//          NULL;
//      }
//      catch (DataException $e) {
//        // If the legal entity number does not exist.
//        $number_key = $this->getElementKey('number', $cardinality, TRUE);
//        $message = $this->wrapErrorMessage('The legal entity number you entered could not be found.', $this->getElementId($number_key, $form));
//        $form_state->setErrorByName($this->getElementName($number_key), $message);
//      }
//      catch (TemporaryException $ignore) {
//        // Users are not responsible for temporary or rate limiting errors.
//      }

      // Validate additional rules if a profile was found.
      parent::validate($form, $form_state, $cardinality, $action);
    }
    else {
      $number_key = $this->getElementKey('registry', $cardinality, TRUE);
      $message = $this->wrapErrorMessage('Please choose whether this is a registered or unregistered legal entity.', $this->getElementId($number_key, $form));
      $form_state->setErrorByName($this->getElementName($number_key), $message);
    }
  }

}
