<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\par_forms\ParSummaryListInterface;
use Drupal\registered_organisations\DataException;
use Drupal\registered_organisations\TemporaryException;

/**
 * Legal Entity form plugin.
 *
 * @ParForm(
 *   id = "legal_entity_summary",
 *   title = @Translation("Legal entity summary form.")
 * )
 */
class ParLegalEntitySummaryForm extends ParFormPluginBase implements ParSummaryListInterface {

  /**
   * {@inheritdoc}
   */
  protected $wrapperName = 'legal entity summary list';

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
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    // This component is designed to complement the `legal_entity` component,
    // allowing multiple legal entities to be added.

    // Data will be returned either structured or flattened depending on the plugin.
    $data = $this->getFlowDataHandler()->getPluginTempData($this);

//    $cid = $this->getFlowNegotiator()->getFormKey('add_legal_entity');
//    $form_data = $this->getFlowDataHandler()->getPluginData($cid, $plugin);
//
//    // Form data is flattened if there is only a single item.
//    if ($form_data[$plugin_prefix])
//    $legal_entity_names = $this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'legal_entity', $cid_[$plugin_id]) ?: [];
//
//    if () {
//
//    }
    //
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
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // For this form plugin only one cardinality should be active at a time.
    $delta = $this->getflowDataHandler()->getParameter('delta') ?? 1;
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    $legal_entity_label = $this->getCardinality() !== 1 ?
      $this->formatPlural($cardinality, 'Legal Entity @index', 'Legal Entity @index (Optional)', ['@index' => $cardinality]) :
      $this->t('Legal Entity');

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

    // Ensure that the correct legal entities are entered for coordinated partnerships.
    if ($this->getFlowDataHandler()->getFormPermValue('coordinated_partnership')) {
      $form['legal_entity_intro_fieldset']['note'] = [
        '#type' => 'markup',
        '#markup' => '<div class="form-group notice">
            <i class="icon icon-important"><span class="visually-hidden">Warning</span></i>
            <strong class="bold-small">Please enter the legal entities for the members covered by this partnership not the co-ordinator.</strong>
          </div>',
      ];
    }

    $form['registry'] = [
      '#type' => 'radios',
      '#title' => 'What type of legal entity is this?',
      '#description' => $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities."),
      '#options' => $registry_options,
      '#options_descriptions' => $registry_options_descriptions,
      '#default_value' => $this->getDefaultValuesByKey('registry', $cardinality, ),
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
    $form['registered']['legal_entity_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the registration number'),
      '#default_value' => $this->getDefaultValuesByKey('legal_entity_number', $cardinality),
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

    $form['unregistered']['legal_entity_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter name of the legal entity'),
      '#default_value' => $this->getDefaultValuesByKey('legal_entity_name', $cardinality),
      '#attributes' => [
        'class' => ['form-group'],
      ],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $registry_element = $this->getElement($form, ['registry'], $cardinality);
    $register_id = $registry_element ? $form_state->getValue($registry_element['#parents']) : NULL;

    if ($register_id === ParDataLegalEntity::DEFAULT_REGISTER) {
      $type_element = $this->getElement($form, ['unregistered','legal_entity_type'], $cardinality);
      $legal_entity_type = $type_element ? $form_state->getValue($type_element['#parents']) : NULL;
      $name_element = $this->getElement($form, ['unregistered','legal_entity_name'], $cardinality);
      $legal_entity_name = $name_element ? $form_state->getValue($name_element['#parents']) : NULL;

      if (empty($legal_entity_type)) {
        $message = 'You must choose which legal entity type you are adding.';
        $this->setError($form, $form_state, $type_element, $message);
      }

      if (empty($legal_entity_name)) {
        $message = 'Please enter the name of the legal entity.';
        $this->setError($form, $form_state, $name_element, $message);
      }

      // Validate additional rules.
      parent::validate($form, $form_state, $cardinality, $action);
    }
    else if (in_array($register_id, ['companies_house', 'charity_commission'])) {
      // Get the legal entity number to look up.
      $number_element = $this->getElement($form, ['registered','legal_entity_number'], $cardinality);
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

      // Validate additional rules if a profile was found.
      parent::validate($form, $form_state, $cardinality, $action);
    }
    else {
      $message = 'Please choose whether this is a registered or unregistered legal entity.';
      $this->setError($form, $form_state, $registry_element, $message);
    }
  }

}
