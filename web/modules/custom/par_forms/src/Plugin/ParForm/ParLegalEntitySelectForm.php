<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Render\Markup;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Legal entity select form plugin.
 *
 * @ParForm(
 *   id = "legal_entity_select",
 *   title = @Translation("Legal entity select form.")
 * )
 */
class ParLegalEntitySelectForm extends ParFormPluginBase {

  const STEP_CHOOSE_EXISTING_LEGAL_ENTITY = 1;
  const STEP_CHOOSE_REGISTRY = 2;
  const STEP_UNREGISTERED_LEGAL_ENTITY_ENTRY = 3;
  const STEP_REGISTRY_SEARCH = 4;
  const STEP_CONFIRMATION = 5;

  /**
   * {@inheritdoc}
   */
  protected $wrapperName = 'legal entity select';

  /**
   * @defaults
   */
  protected $formDefaults = [
  ];

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    // Get the plugin state.
    $state = $this->getFlowDataHandler()->getMetaDataValue($this->getPluginId() . ':state');

    // First time in.   that are not already part of the
    // partnership.
    if (empty($state['step'])) {

      // Initialise state.
      $state['step'] = self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY;
      $state['existing_legal_entities'] = [];
      $state['selected_legal_entities'] = [];
      $state['search_results'] = [];
      $state['search_term'] = '';

      // Get the set of list of legal entities already attached to the organisation.
      /* @var \Drupal\par_data\Entity\ParDataPartnership $partnership */
      $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
      $organisation = !empty($partnership) ? $partnership->getOrganisation(TRUE) : NULL;
      $existing_legal_entities = !empty($organisation) ? $organisation->getLegalEntity() : [];

      // Remove legal entities already attached to the partnership.
      $partnership_legal_entities = $partnership->getLegalEntity();
      foreach ($existing_legal_entities as $key => $existing_legal_entity) {
        foreach ($partnership_legal_entities as $partnership_legal_entity) {
          if ($existing_legal_entity === $partnership_legal_entity) {
            unset($existing_legal_entities[$key]);
            break;
          }
        }
      }

      // Put remaining existing legal entities, those that may be added to the partnership, into the state array.
      foreach ($existing_legal_entities as $existing_legal_entity) {
        $state['existing_legal_entities'][] = (object)[
          'entity_id' => $existing_legal_entity->id(),
          'registry' => $existing_legal_entity->getRegistry(),
          'name' => $existing_legal_entity->getName(),
          'type' => $existing_legal_entity->getType(),
          'number' => $existing_legal_entity->getRegisteredNumber(),
        ];
      }
    }

    switch ($state['step']) {

      case self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY:

        // There are existing entities that can be selected.
        if (!empty($state['existing_legal_entities'])) {

          // Construct the options list.
          $options = [];
          foreach ($state['existing_legal_entities'] as $key => $existing_legal_entity) {
            $options[$key] =
              $existing_legal_entity->name . ' ' .
              $existing_legal_entity->type . ' ' .
              $existing_legal_entity->number;
          }

          // Add the 'add other' option
          $options['add_other'] = 'No, I want to add a different legal entity';

          // Add the checkboxes control.
          $form['existing_legal_entities'] = [
            '#type' => 'checkboxes',
            '#title' => 'Choose from the organisation\'s existing legal entities',
            '#description' => '<p>Select legal entities that are already attached to the organisation but are not part of the partnership.</p>',
            '#options' => $options,
            '#attributes' => ['class' => ['form-group']],
            'add_other' => [
              '#prefix' => Markup::create('<div class="govuk-checkboxes__divider">or</div>'),
            ],
          ];

          $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Continue');
          break;
        }

        // There are no existing legal entities to select from. We drop through to the next step.
        $state['step'] = self::STEP_CHOOSE_REGISTRY;

      case self::STEP_CHOOSE_REGISTRY:

        $form['registry'] = [
          '#type' => 'radios',
          '#title' => 'What type of legal entity do you want to add?',
          '#options' => [
            'companies_house' => 'A registered organisation',
            'charity_commission' => 'A charity',
            'internal' => 'An unregistered organisation',
          ],
          'companies_house' => [
            '#description' => 'Please choose this option if the organisation or partnership is registered with Companies House.',
          ],
          'charity_commission' => [
            '#description' => 'Please choose this option if the charity is registered with the Charity Commission.',
          ],
          'internal' => [
            '#description' => 'Please choose this option for all other legal entity types.',
          ],
        ];

        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Continue');

        break;

      case self::STEP_UNREGISTERED_LEGAL_ENTITY_ENTRY:

        $form['legal_entity_name'] = [
          '#type' => 'textfield',
          '#title' => 'Name',
          '#required' => TRUE,
        ];

        $form['legal_entity_type'] = [
          '#type' => 'radios',
          '#title' => 'What type of organisation is this?',
          '#options' => [
            'partnership' => 'Partnership',
            'sole_trader' => 'Sole trader',
            'unincorporated_association' => 'Unincorporated association',
            'other' => 'Other',
          ],
          '#required' => TRUE,
          'partnership' => [
            '#description' => 'A partnership is a contractual arrangement between two or more people that is set up with a view to profit and to share the profits amongst the partners.',
          ],
          'sole_trader' => [
            '#description' => 'A sole trader is an individual who is registered with HMRC for tax purposes.',
          ],
          'unincorporated_association' => [
            '#description' => 'A simple way for a group of volunteers to run an organisation for a common purpose.',
          ],
        ];

        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Continue');

        break;

      case self::STEP_REGISTRY_SEARCH:

        // We have search results.
        if (!empty($state['search_results'])) {

          /* @var \Drupal\registered_organisations\OrganisationManager $om */
          $om = \Drupal::service('registered_organisations.organisation_manager');
          $def = $om->getDefinition($state['registry']);
          $plugin = $om->createInstance($state['registry']);
          $x = $plugin::COMPANY_TYPE;

          $options = [];
          $descriptions = [];
          foreach ($state['search_results'] as $key => $value) {
            $options[$key] = $value->name;
            $parts = [];
            if (isset($plugin::COMPANY_TYPE[$value->type])) {
              $parts[] = $plugin::COMPANY_TYPE[$value->type];
            }
            if (!empty($value->number)) {
              $parts[] = $value->number;
            }
            $descriptions[$key] = implode(' - ', $parts);
          }
          $form['search_results'] = [
            '#type' => 'checkboxes',
            '#title' => 'Choose legal entities',
            '#description' => 'We have found ' . count($state['search_results']) . ' matching legal entities in the register.',
            '#options' => $options,
          ];
          foreach ($descriptions as $key => $description) {
            $form['search_results'][$key] = [
              '#description' => $description,
            ];
          }

          // Button to continue having selected from search results.
          $form['choose_submit'] = [
            '#type' => 'button',
            '#name' => 'choose_submit',
            '#value' => 'Continue',
          ];
        }

        // The search text box.
        $form['search_term'] = [
          '#type' => 'textfield',
          '#title' => 'What is the name of the legal entity?',
          '#default_value' => $state['search_term'],
          '#required' => empty($state['search_results']),
        ];

        $form['search_help'] = [
          '#type' => 'details',
          '#title' => 'Help with searching',
        ];
        $url = 'https://find-and-update.company-information.service.gov.uk/advanced-search';
        $form['search_help']['advice'] = [
          '#type' => 'container',
          'p-1' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => 'The search of the Companies House register tries to match all the words ' .
                        'entered and returns the closest 20 results. If you are having difficulty ' .
                        'finding the legal entity try variations of spelling.',
          ],
          'p-2' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => 'We only search the active register. If the legal entity is closed or in ' .
                        'receivership it will not be found.',
          ],
          'p-3' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => 'Companies House provides an <a href="' . $url . '">advanced company search</a>. This ' .
                        'has many more search options, and searches both active and closed registers.',
          ],
        ];

        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Search');

        break;

      case self::STEP_CONFIRMATION:

        $form['confirm_entities'] = [
          '#type' => 'table',
          '#header' => [
            'Legal entity',
            'Action',
          ],
        ];

        // Add a row for selected legal entity.
        foreach ($state['selected_legal_entities'] as $delta => $selected_legal_entity) {

          $form['confirm_entities'][$delta]['legal_entity'] = [
            '#type' => 'container',
            '#attributes' => ['class' => 'column-full'],
            'name' => [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => $selected_legal_entity->name,
            ],
            'type_number' => [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => implode(' ', [$selected_legal_entity->type, $selected_legal_entity->number]),
            ],
          ];

          $form['confirm_entities'][$delta]['operations'] = [
            '#type' => 'container',
            'remove' => [
              '#type' => 'button',
              '#name' => 'remove-' . $delta,
              '#value' => 'remove',
              '#attributes' => ['class' =>['btn-link']]
            ],
          ];
        }

        // Add another button.
        $form['select_another'] = [
          '#type' => 'button',
          '#name' => 'select-another',
          '#value' => 'select another legal entity',
          '#attributes' => ['class' =>['btn-link']]
        ];

        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Confirm');

        break;

      default:
        throw new ParFlowException('Invalid step ' . $state['step'] . ' in ' . __CLASS__ . '::' . __METHOD__ . '.');
    }

    // Save plugin state.
    $this->getFlowDataHandler()->setMetaDataValue($this->getPluginId() . ':state', $state);

    return $form;
  }

  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {

    $state = $this->getFlowDataHandler()->getMetaDataValue($this->getPluginId() . ':state');

    parent::validate($form, $form_state, $cardinality, $action);

    switch ($state['step']) {

      case self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY:
        $options = $form_state->getValue('existing_legal_entities');
        $chosen = [];
        foreach ($options as $key => $val) {
          if (is_string($val)) {
            $chosen[$key] = $val;
          }
        }
        if (empty($chosen)) {
          $id = $this->getElementId(['existing_legal_entities'], $form);
          $form_state->setErrorByName($this->getElementName('existing_legal_entities'), $this->wrapErrorMessage('Please make a selection.', $id));
          break;
        }
        if (count($chosen) > 1 && isset($chosen['add_other'])) {
          $id = $this->getElementId(['existing_legal_entities'], $form);
          $form_state->setErrorByName($this->getElementName('existing_legal_entities'), $this->wrapErrorMessage('Please choose only existing entities or the option to add a different entity, not both.', $id));
          break;
        }

        // User has chosen to ignore the existing legal entities and add a new one.
        if (isset($chosen['add_other'])) {
          $state['step'] = self::STEP_CHOOSE_REGISTRY;
          $form_state->setRebuild();
          break;
        }

        // User has selected at least one of the existing legal entities. Add to the selected list and remove from
        // existing list.
        foreach ($chosen as $key => $value) {
          $state['selected_legal_entities'][] = $state['existing_legal_entities'][$key];
          unset($state['existing_legal_entities'][$key]);
        }

        // Send the user to the confirmation step.
        $state['step'] = self::STEP_CONFIRMATION;
        $form_state->setRebuild();
        break;

      case self::STEP_CHOOSE_REGISTRY:

        $registry = $form_state->getValue('registry');
        if (empty($registry)) {
          $id = $this->getElementId(['registry'], $form);
          $form_state->setErrorByName($this->getElementName('registry'), $this->wrapErrorMessage('Please choose one of the options.', $id));
          break;
        }

        $state['registry'] = $registry;

        switch ($state['registry']) {
          case 'companies_house':
            $state['step'] = self::STEP_REGISTRY_SEARCH;
            break;
          case 'charity_commission':
            $state['step'] = self::STEP_REGISTRY_SEARCH;
            break;
          case 'internal':
            $state['step'] = self::STEP_UNREGISTERED_LEGAL_ENTITY_ENTRY;
            break;
        }

        $form_state->setRebuild();
        break;

      case self::STEP_UNREGISTERED_LEGAL_ENTITY_ENTRY:

        $state['selected_legal_entities'][] = (object)[
          'entity_id' => 0,
          'registry' => 'internal',
          'name' => $form_state->getValue('legal_entity_name'),
          'type' => $form_state->getValue('legal_entity_type'),
          'number' => NULL,
        ];

        $state['step'] = self::STEP_CONFIRMATION;
        $form_state->setRebuild();

        break;

      case self::STEP_REGISTRY_SEARCH:

        $triggeredElement = $form_state->getTriggeringElement();

        // Handle selection from results..
        if ($triggeredElement['#name'] == 'choose_submit') {

          // Get chosen search results, if any.
          $search_results = $form_state->getValue('search_results', []);
          $chosen = [];
          foreach ($search_results as $key => $val) {
            if (is_string($val)) {
              $chosen[] = $key;
            }
          }

          // Error if nothing chosen.
          if (empty($chosen)) {
            $id = $this->getElementId(['search_results'], $form);
            $form_state->setErrorByName($this->getElementName('search_results'), $this->wrapErrorMessage('Please choose one of the options.', $id));
            break;
          }

          // Copy results to selected array.
          foreach ($chosen as $key => $value) {
            $state['selected_legal_entities'][] = $state['search_results'][$key];
          }

          // Clear search term and results.
          $state['search_term'] = '';
          $state['search_results'] = [];

          // Go to confirmation step.
          $state['step'] = self::STEP_CONFIRMATION;
          $form_state->setRebuild();
          break;
        }

        // No search results chosen. Do a search.
        $state['search_term'] = $form_state->getValue('search_term', '');

        /* @var \Drupal\registered_organisations\OrganisationManager $om */
        $om = \Drupal::service('registered_organisations.organisation_manager');
        $defs = $om->getDefinitions();
        $results = $om->searchOrganisation($state['search_term'], $defs[$state['registry']]);

        $state['search_results'] = [];
        foreach ($results as $result) {

          // @todo Filter out LEs that we already have in our selected list or are already attached to the org.

          $state['search_results'][] = (object)[
            'entity_id' => 0,
            'registry' => $result->getRegister(),
            'name' => $result->getName(),
            'type' => $result->getType(),
            'number' => $result->getId(),
          ];
        }

        $form_state->setRebuild();
        break;

      case self::STEP_CONFIRMATION:

        $triggeredElement = $form_state->getTriggeringElement();

        // Handle the clicking of remove buttons.
        if (mb_substr($triggeredElement['#name'], 0, 7) == 'remove-') {

          // Remove this legal entity from the list of selected legal entities.
          // If it is an existing organisation legal entity put it back in the existing list for reselection.
          $ind = mb_substr($triggeredElement['#name'], 7);
          if ($state['selected_legal_entities'][$ind]->entity_id) {
            $state['existing_legal_entities'][] = $state['selected_legal_entities'][$ind];
          }
          unset($state['selected_legal_entities'][$ind]);

          // If the last legal entity has been removed then go to first step so user can start again. Otherwise, we
          // just redisplay this step.
          if (count($state['selected_legal_entities']) < 1) {
            $state['step'] = self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY;
          }

          $form_state->setRebuild();
          break;
        }

        // Handle 'add another' button. Send the user to the first step.
        if ($triggeredElement['#name'] == 'select-another') {
          $state['step'] = self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY;
          $form_state->setRebuild();
        }

        // User has confirmed the chosen legal entities.

        break;

      default:
        throw new ParFlowException('Invalid step ' . $state['step'] . ' in ' . __CLASS__ . '::' . __METHOD__ . '.');
    }

    // Save plugin state.
    $this->getFlowDataHandler()->setMetaDataValue($this->getPluginId() . ':state', $state);
  }
}
