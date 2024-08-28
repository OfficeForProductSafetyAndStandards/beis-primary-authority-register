<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\par_forms\ParSummaryListInterface;
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
class ParLegalEntityForm extends ParFormPluginBase implements ParSummaryListInterface {

  /**
   * How many items should be displayed in a summary list.
   */
  const NUMBER_ITEMS = 10;

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['registry', 'par_data_legal_entity', 'registry', NULL, NULL, 0, [
      'This value should not be null.' => 'You must choose the type of legal entity.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  protected string $wrapperName = 'legal entity';

  /**
   * @defaults
   */
  protected array $formDefaults = [
    'legal_entity_type' => 'none',
  ];

  /**
   * Get the registered organisation manager.
   */
  public function getOrganisationManager() {
    return \Drupal::service('registered_organisations.organisation_manager');
  }

  /**
   * Load the data for this form.
   */
  public function loadData(int $index = 1): void {
    if ($par_data_legal_entity = $this->getFlowDataHandler()->getParameter('par_data_legal_entity')) {
      // Set the registry id.
      $this->setDefaultValuesByKey(['registry'], $index, $par_data_legal_entity->getRegisterId());

      // Set the requisite pieces of data depending on the type of legal entity.
      switch ($par_data_legal_entity->getRegisterId()) {
        case 'companies_house':
        case 'charity_commission':

          $this->setDefaultValuesByKey(['registered','legal_entity_number'], $index, $par_data_legal_entity->getRegisteredNumber());
          break;

        case ParDataLegalEntity::DEFAULT_REGISTER:
          $unregistered_types = ['partnership', 'sole_trader', 'unincorporated_association', 'other'];
          if (in_array($par_data_legal_entity->getType(), $unregistered_types)) {
            $this->setDefaultValuesByKey(['unregistered', 'legal_entity_type'], $index, $par_data_legal_entity->getType());
          }
          $this->setDefaultValuesByKey(['unregistered','legal_entity_name'], $index, $par_data_legal_entity->getName());
          break;

        default:
          $this->setDefaultValuesByKey(['registered','legal_entity_number'], $index, $par_data_legal_entity->getRegisteredNumber());

      }
    }

    if ($par_data_partnership = $this->getflowDataHandler()->getParameter('par_data_partnership')) {
      $this->getFlowDataHandler()->setFormPermValue('coordinated_partnership', $par_data_partnership->isCoordinated());
    }

    parent::loadData($index);
  }

  /**
   * Apply additional filtering to each instance so that incomplete plugin data
   * is not submitted when validation is bypassed.
   *
   * {@inheritdoc}
   */
  public function filterItem(array $item): array {
    $item = parent::filterItem($item);

    // If a registry value is set but no other accompanying values then consider
    // the instance incomplete and clear the data.
    $registry_key = $this->getItemKey(['registry']);
    $name_key = $this->getItemKey(['unregistered', 'legal_entity_name']);
    $number_key = $this->getItemKey(['registered', 'legal_entity_number']);
    $type_key = $this->getItemKey(['unregistered', 'legal_entity_type']);

    // Do not provide extra filtering if no registry value is set.
    $registry = NestedArray::getValue($item, $registry_key);
    if (empty($registry)) {
      return $item;
    }

    $name = NestedArray::getValue($item, $name_key);
    $type = NestedArray::getValue($item, $type_key);
    $number = NestedArray::getValue($item, $number_key);

    // Unset the registry key if the required values are not completed.
    if ($registry === ParDataLegalEntity::DEFAULT_REGISTER &&
      (empty($name) || empty($type))) {
      NestedArray::unsetValue($item, $registry_key);
    }
    else if (in_array($registry, ['companies_house', 'charity_commission']) &&
      empty($number)) {
      NestedArray::unsetValue($item, $registry_key);
    }

    return $item;
  }

  /**
   * {@inheritDoc}
   */
  public function getSummaryList(array $form = []): mixed {
    // Get a number formatter to display the row values in words.
    $formatter = new \NumberFormatter('en_UK', \NumberFormatter::SPELLOUT);
    $formatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, "%spellout-ordinal");

    $form['list'] = [
      '#type' => 'html_tag',
      '#tag' => 'dl',
      '#attributes' => ['class' => ['govuk-summary-list']],
    ];

    // Get the data.
    $data = $this->getData();

    // Get the pager.
    $pager = $this->getUniquePager()->getPager($this->getPluginId());
    $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($data), self::NUMBER_ITEMS, $pager);
    $form['list']['pager'] = [
      '#type' => 'pager',
      '#theme' => 'pagerer',
      '#element' => $pager,
      '#weight' => 99,
      '#config' => [
        'preset' => $this->config('pagerer.settings')
          ->get('core_override_preset'),
      ],
    ];

    // Split the items up into chunks.
    $chunks = array_chunk($data, self::NUMBER_ITEMS, TRUE);
    $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];

    // Display the legal entities.
    foreach ($chunk as $delta => $row) {
      $index = $delta + 1;

      // Turn the data into a legal entity.
      $values = [
        'registry' => $this->getDefaultValuesByKey(['registry'], $index,  ParDataLegalEntity::DEFAULT_REGISTER),
        'registered_name' => $this->getDefaultValuesByKey(['unregistered', 'legal_entity_name'], $index,  ''),
        'registered_number' => trim($this->getDefaultValuesByKey(['registered', 'legal_entity_number'], $index,  '')),
        'legal_entity_type' => $this->getDefaultValuesByKey(['unregistered', 'legal_entity_type'], $index,  ''),
      ];
      $legal_entity = ParDataLegalEntity::create($values);
      // Lookup the correct values for registered entities.
      $legal_entity->lookup();

      // Display the row data as an item.
      $view_builder = \Drupal::entityTypeManager()
        ->getViewBuilder($legal_entity->getEntityTypeId());
      $item = $view_builder
        ->view($legal_entity, 'summary');

      $form['list'][$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-summary-list__row']],
        'key' => [
          '#type' => 'html_tag',
          '#tag' => 'dt',
          '#value' => $this->t("@number legal entity", ['@number' => ucfirst($formatter->format($index))]),
          '#attributes' => ['class' => ['govuk-summary-list__key']],
        ],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'dd',
          '#attributes' => ['class' => ['govuk-summary-list__value']],
          [...$item],
        ],
        'actions' => [
          '#type' => 'html_tag',
          '#tag' => 'dd',
          '#attributes' => ['class' => ['govuk-summary-list__actions']],
        ],
      ];

      // Get the supported actions.
      if ($element_actions = $this->getElementActions($index)) {
        $form['list'][$delta]['actions'][] = $element_actions;
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $registry_options = [
      'companies_house' => 'A registered organisation',
      'charity_commission' => 'A charity',
      ParDataLegalEntity::DEFAULT_REGISTER => 'An unregistered entity',
      'ch_as_different_type' => 'A registered organisation or charity originally registered as a different type',
    ];

    $registry_options_descriptions = [
      'companies_house' => 'Please choose this option if the organisation or partnership is registered with Companies House.',
      'charity_commission' => 'Please choose this option if the charity is registered with the Charity Commission but isn\'t a registered company.',
      ParDataLegalEntity::DEFAULT_REGISTER => 'Please choose this option for sole traders and all other legal entity types.',
      'ch_as_different_type' => 'Please choose this option if the organisation or charity was registered as a different type.',
    ];

    // Ensure that the correct legal entities are entered for coordinated partnerships.
    if ($this->getFlowDataHandler()->getFormPermValue('coordinated_partnership')) {
      $form['legal_entity_intro_fieldset'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => ['class' => ['govuk-warning-text']],
        'icon' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => '!',
          '#attributes' => [
            'class' => ['govuk-warning-text__icon'],
            'aria-hidden' => 'true',
          ],
        ],
        'strong' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t('Please enter the legal entities for the members covered by this partnership not the co-ordinator.'),
          '#attributes' => ['class' => ['govuk-warning-text__text']],
          'message' => [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => $this->t('Warning'),
            '#attributes' => ['class' => ['govuk-warning-text__assistive']],
          ],
        ]
      ];
    }

    $form['registry'] = [
      '#type' => 'radios',
      '#title' => 'What type of legal entity is this?',
      '#title_tag' => 'h2',
      '#description' => $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities."),
      '#options' => $registry_options,
      '#options_descriptions' => $registry_options_descriptions,
      '#default_value' => $this->getDefaultValuesByKey('registry', $index, ),
      '#after_build' => [
        [get_class($this), 'optionsDescriptions'],
      ],
      '#attributes' => [
        'class' => ['govuk-form-group'],
      ],
    ];

    // Follow-up inputs for registered entities.
    $form['registered'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          'input[name="' . $this->getTargetName($this->getElementKey('registry', $index)) . '"]' => [
            ['value' => 'companies_house'],
            ['value' => 'charity_commission'],
          ],
        ],
      ],
      '#attributes' => [
        'class' => ['govuk-form-group', 'govuk-radios__conditional'],
      ],
    ];

    $form['registered']['legal_entity_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the registration number'),
      '#default_value' => $this->getDefaultValuesByKey(['registered', 'legal_entity_number'], $index),
    ];

    // Follow-up inputs for unregistered entities.
    $form['unregistered'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          'input[name="' . $this->getTargetName($this->getElementKey('registry', $index)) . '"]' => [
            ['value' => ParDataLegalEntity::DEFAULT_REGISTER],
          ],
        ],
      ],
      '#attributes' => [
        'class' => ['govuk-form-group', 'govuk-radios__conditional'],
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
      '#title_tag' => 'h3',
      '#default_value' => $this->getDefaultValuesByKey(['unregistered', 'legal_entity_type'], $index),
      '#options' => $unregistered_type_options,
      '#options_descriptions' => $unregistered_type_options_descriptions,
      '#after_build' => [
        [get_class($this), 'optionsDescriptions'],
      ],
      '#states' => [
        'checked' => [
          'input[name="' . $this->getTargetName($this->getElementKey('ch_as_different_type', $index)) . '"]' => [
            ['value' => 'other'],
          ],
        ],
      ],
      '#attributes' => [
        'class' => ['govuk-radios--small', 'govuk-form-group'],
      ],
    ];

    $form['unregistered']['legal_entity_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter name of the legal entity'),
      '#default_value' => $this->getDefaultValuesByKey(['unregistered', 'legal_entity_name'], $index),
      '#attributes' => [
        'class' => ['govuk-form-group'],
      ],
    ];

    $form['ch_as_different_type'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          'input[name="' . $this->getTargetName($this->getElementKey('registry', $index)) . '"]' => [
            ['value' => 'ch_as_different_type'],
          ],
        ],
      ],
      '#attributes' => [
        'class' => ['govuk-form-group', 'govuk-radios__conditional'],
      ],
    ];

    $ch_different_type_options = [
      'charity_now_companies_house' => 'A previously registered charity that is now registered with companies house',
      'organisation_now_charities_commission' => 'A previously registered organisation that is now registered with the charities commission',
    ];

    $form['ch_as_different_type']['ch_different_type_definition'] = [
      '#type' => 'radios',
      '#title' => $this->t('Organisation or charity type definition'),
      '#title_tag' => 'h3',
      '#options' => $ch_different_type_options,
      '#default_value' => $this->getDefaultValuesByKey([
        'ch_as_different_type',
        'ch_different_type_definition',
      ], $index),
      '#attributes' => [
        'class' => ['govuk-radios--small', 'govuk-form-group'],
      ],
    ];

    $form['ch_as_different_type']['ch_legal_entity_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter name of the legal entity'),
      '#default_value' => $this->getDefaultValuesByKey(['ch_as_different_type', 'legal_entity_name'], $index),
      '#attributes' => [
        'class' => ['govuk-form-group'],
      ],
    ];

    $form['ch_as_different_type']['ch_legal_entity_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the registration number'),
      '#default_value' => $this->getDefaultValuesByKey(['ch_as_different_type', 'ch_legal_entity_number'], $index),
      '#attributes' => [
        'class' => ['govuk-form-group'],
      ],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $registry_element = $this->getElement($form, ['registry'], $index);
    $register_id = $registry_element ? $form_state->getValue($registry_element['#parents']) : NULL;

    // For multi cardinality plugin instances get the existing plugin data
    // to validate against.
    if ($this->isMultiple()) {
      $existing_data = $this->getFlowDataHandler()->getPluginTempData($this);
      // Ignore the current index.
      $delta = $index - 1;
      if (isset($existing_data[$delta])) {
        unset($existing_data[$delta]);
      }
    }

    if ($register_id === ParDataLegalEntity::DEFAULT_REGISTER) {
      $type_element = $this->getElement($form, ['unregistered', 'legal_entity_type'], $index);
      $legal_entity_type = $type_element ? $form_state->getValue($type_element['#parents']) : NULL;
      $name_element = $this->getElement($form, ['unregistered', 'legal_entity_name'], $index);
      $legal_entity_name = $name_element ? trim((string) $form_state->getValue($name_element['#parents'])) : NULL;

      if (empty($legal_entity_type)) {
        $message = 'You must choose which legal entity type you are adding.';
        $this->setError($form, $form_state, $type_element, $message);
      }

      if (empty($legal_entity_name)) {
        $message = 'Please enter the name of the legal entity.';
        $this->setError($form, $form_state, $name_element, $message);
      }

      // Check that this legal entity isn't already used by another item.
      if (isset($existing_data)) {
        foreach ($existing_data as $item) {
          $item_name = NestedArray::getValue($item, ['unregistered', 'legal_entity_name']);
          $item_name = trim((string) $item_name);
          if ($legal_entity_name === $item_name) {
            $message = 'This legal entity has already been added.';
            $this->setError($form, $form_state, $name_element, $message);
          }
        }
      }

      // Validate additional rules.
      parent::validate($form, $form_state, $index, $action);
    }
    elseif (in_array($register_id, ['companies_house', 'charity_commission'])) {
      // Get the legal entity name.
      $name_element = $this->getElement($form, [$register_id, 'legal_entity_name'], $index);
      $legal_entity_name = $name_element ? trim((string) $form_state->getValue($name_element['#parents'])) : NULL;

      // Get the legal entity number to look up.
      $number_element = $this->getElement($form, ['registered', 'legal_entity_number'], $index);
      $legal_entity_number = $number_element ? trim((string) $form_state->getValue($number_element['#parents'])) : NULL;

      if (empty($legal_entity_number)) {
        // Invalidate the submission if no legal entity number is provided.
        $message = 'Please enter the legal entity number.';
        $this->setError($form, $form_state, $number_element, $message);
      }
      else {
        // Attempt to validate the legal entity number with a registry lookup.
        try {
          $registry = $this->getOrganisationManager()->getRegistry($register_id);
          if ($registry->isValidId($legal_entity_number)) {
            // Only look up valid IDs to reduce the number of API requests.
            $this->getOrganisationManager()->lookupOrganisation($register_id, $legal_entity_number);
          }
          else {
            // The legal entity number is in an invalid format.
            $message = 'The legal entity number you entered is not valid.';
            $this->setError($form, $form_state, $number_element, $message);
          }
        }
        catch (DataException $e) {
          // If the legal entity number does not exist in the registry.
          $message = 'The legal entity number you entered could not be found.';
          $this->setError($form, $form_state, $number_element, $message);
        }
        catch (TemporaryException $ignore) {
          // Users are not responsible for temporary or rate limiting errors.
        }
        catch (PluginNotFoundException $ignore) {
          // Ignore system errors.
        }
      }

      // Check that this legal entity isn't already used by another item.
      if (isset($existing_data)) {
        foreach ($existing_data as $item) {
          $item_number = NestedArray::getValue($item, ['registered','legal_entity_number']);
          $item_number = trim((string) $item_number);
          if ($legal_entity_number === $item_number) {
            $message = 'This legal entity has already been added.';
            $this->setError($form, $form_state, $number_element, $message);
          }
        }
      }

      // Validate additional rules if a profile was found.
      parent::validate($form, $form_state, $index, $action);
    }
    elseif ($register_id == 'ch_as_different_type') {
      // Get the legal entity name.
      $name_element = $this->getElement($form, ['ch_as_different_type', 'ch_legal_entity_name'], $index);
      $legal_entity_name = $name_element ? trim((string) $form_state->getValue($name_element['#parents'])) : NULL;

      // Get the legal entity number to look up.
      $number_element = $this->getElement($form, ['ch_as_different_type', 'ch_legal_entity_number'], $index);
      $legal_entity_number = $number_element ? trim((string) $form_state->getValue($number_element['#parents'])) : NULL;

      // Get the entity type definition.
      $definition_element = $this->getElement($form, [
        'ch_as_different_type',
        'ch_different_type_definition',
      ], $index);
      $type_definition = $definition_element ? $form_state->getValue($definition_element['#parents']) : NULL;

      // Validate the type definition.
      if (empty($type_definition)) {
        $message = 'Please enter the organisation or charity type definition.';
        $this->setError($form, $form_state, $definition_element, $message);
      }

      // Validate the legal entity name.
      if (empty($legal_entity_name)) {
        // Invalidate the submission if no legal entity name is provided.
        $message = 'Please enter the legal entity name.';
        $this->setError($form, $form_state, $name_element, $message);
      }

      // Validate the legal entity number.
      if (empty($legal_entity_number)) {
        // Invalidate the submission if no legal entity number is provided.
        $message = 'Please enter the legal entity number.';
        $this->setError($form, $form_state, $number_element, $message);
      }
      else {
        $registry_id = $type_definition == 'charity_now_companies_house' ? 'charity_commission' : 'companies_house';
        $registry = $this->getOrganisationManager()->getRegistry($registry_id);

        if (!$registry->isValidId($legal_entity_number)) {
          $message = 'The legal entity number you entered is not valid.';
          $this->setError($form, $form_state, $number_element, $message);
        }
      }

      // Validate additional rules if a profile was found.
      parent::validate($form, $form_state, $index, $action);
    }
    elseif ($registry_element) {
      $message = 'Please choose whether this is a registered or unregistered legal entity.';
      $this->setError($form, $form_state, $registry_element, $message);
    }
  }

}
