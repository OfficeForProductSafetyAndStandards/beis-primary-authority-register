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

    // First time in set initial step.
    if (empty($state['step'])) {
      $state['step'] = self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY;
    }

    switch ($state['step']) {

      case self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY:

        // Get the set of existing legal entities that the user can select. These are the set of legal entities
        // attached to organisation that are not already part of the partnership and have not already been selected by
        // a previous pass through this plugin.
        /* @var \Drupal\par_data\Entity\ParDataPartnership $partnership */
        $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
        $organisation = !empty($partnership) ? $partnership->getOrganisation(TRUE) : NULL;
        $state['existing_legal_entities'] = !empty($organisation) ? $organisation->getLegalEntity() : [];
        if (!empty($state['existing_legal_entities'])) {

          // Remove legal entities that are already on the partnership.
          $partnership_legal_entities = $partnership->getLegalEntity();
          foreach ($partnership_legal_entities as $partnership_legal_entity) {
            foreach ($state['existing_legal_entities'] as $key => $existing_legal_entity) {
              if ($partnership_legal_entity === $existing_legal_entity) {
                unset($state['existing_legal_entities'][$key]);
              }
            }
          }

          // Remove legal entities that have already been selected.
          foreach ($state['selected_legal_entities'] as $selected_legal_entity) {
            foreach ($state['existing_legal_entities'] as $key => $existing_legal_entity) {
              if ($selected_legal_entity === $existing_legal_entity) {
                unset($state['selected_legal_entities'][$key]);
              }
            }
          }
        }

        // There are existing entities that can be selected.
        if (!empty($state['existing_legal_entities'])) {

          // Construct the options list.
          $options = [];
          foreach ($state['existing_legal_entities'] as $existing_legal_entity) {
            $options[] =
              $existing_legal_entity->getName() . ' ' .
              $existing_legal_entity->getType() . ' ' .
              $existing_legal_entity->getRegisteredNumber();
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
          '#options_descriptions' => array(
            'companies_house' => 'Please choose this option if the organisation or partnership is registered with Companies House.',
            'charity_commission' => 'Please choose this option if the charity is registered with the Charity Commission.',
            'internal' => 'Please choose this option for all other legal entity types.',
          ),
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
          '#options_descriptions' => array(
            'partnership' => 'A partnership is a contractual arrangement between two or more people that is set up with a view to profit and to share the profits amongst the partners.',
            'sole_trader' => 'A sole trader is an individual who is registered with HMRC for tax purposes.',
            'unincorporated_association' => 'A simple way for a group of volunteers to run an organisation for a common purpose.',
          ),
          '#required' => TRUE,
        ];

        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Continue');

        break;

      case self::STEP_REGISTRY_SEARCH:

        if (!isset($state['search_name'])) {
          $state['search_name'] = '';
        }
        if (!isset($state['search_results'])) {
          $state['search_results'] = [];
        }

        $form['search_name'] = [
          '#type' => 'textfield',
          '#title' => 'What is the name of the legal entity?',
          '#default_value' => $state['search_name'],
          '#required' => empty($state['search_results']),
        ];

        $form['search_help'] = [
          '#type' => 'details',
          '#title' => 'Help with search',
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

        // First time through or no results found. All the user can do is search.
        if (empty($state['search_results'])) {
          $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Search');
        }

        // Results found.
        else {

          // Secondary button to do another search.
          $form['search_submit'] = [
            '#type' => 'button',
            '#name' => 'search_submit',
            '#value' => 'Search again',
          ];

          // List of results. User chooses legal entities they want.
          $search_results = [];
          foreach ($state['search_results'] as $key => $value) {
            $search_results[$key] = $value['name'] . ' - ' . $value['type'] . ' - ' . $value['number'];
          }
          $form['search_results'] = [
            '#type' => 'checkboxes',
            '#title' => 'Choose the legal entity',
            '#description' => 'We have found ' . count($search_results) . 'matching legal entities in the register.',
            '#options' => $search_results,
            '#attributes' => ['class' => ['form-group']],
          ];

          $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Continue');
        }

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

        // User has selected at least one of the existing legal entities to add to the selected list.
        foreach ($chosen as $key => $value) {
          /* @var \Drupal\par_data\Entity\ParDataLegalEntity $legal_entity */
          $legal_entity = $state['existing_legal_entities'][$key];
          $state['selected_legal_entities'][] = (object)[
            //'registry' => $legal_entity->getRegistry(),
            'name' => $legal_entity->getName(),
            'type' => $legal_entity->getType(),
            'number' => $legal_entity->getRegisteredNumber(),
          ];
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
          //'registry' => 'internal',
          'name' => $form_state->getValue('legal_entity_name'),
          'type' => $form_state->getValue('legal_entity_type'),
          'number' => NULL,
        ];

        $state['step'] = self::STEP_CONFIRMATION;
        $form_state->setRebuild();

        break;

      case self::STEP_REGISTRY_SEARCH:

        // Get chosen search results, if any.
        $search_results = $form_state->getValue('search_results', []);
        $chosen = [];
        foreach ($search_results as $key => $val) {
          if (is_string($val)) {
            $chosen[] = $key;
          }
        }

        // No search results chosen. Do a search.
        if (empty($chosen)) {

          $state['search_name'] = $form_state->getValue('search_name', '');

          // @TODO Replace this stub with real query to registry.
          $search_results = [
            0 => [
              'registry' => $state['registry'],
              'name' => 'Some Company Ltd',
              'type' => 'limited_company',
              'number' => '34343434',
            ],
            1 => [
              'registry' => $state['registry'],
              'name' => 'Some Other Business Ltd',
              'type' => 'limited_company',
              'number' => '56565656',
            ],
            2 => [
              'registry' => $state['registry'],
              'name' => 'The Alternative Business Ltd',
              'type' => 'limited_company',
              'number' => '78787878',
            ],
            3 => [
              'registry' => $state['registry'],
              'name' => 'A Real Trustworthy Business Ltd',
              'type' => 'limited_company',
              'number' => '90909090',
            ],
          ];

          // @todo Filter out LEs that we already have in our selected list or are already attached to the org.
          $state['search_results'] = $search_results;
        }

        // Businesses selected from results.
        else {

          // Add these legal entities to the selected list.
          foreach ($chosen as $key) {

            $state['selected_legal_entities'] = (object)[
              'registry' => $state['search_results'][$key]['registry'],
              'name' => $state['search_results'][$key]['name'],
              'type' => $state['search_results'][$key]['type'],
              'number' => $state['search_results'][$key]['number'],
            ];
          }

          // Clear search values so that search elements will be blank if user chooses to do another search.
          unset($state['search_name']);
          unset($state['search_results']);

          $state['step'] = self::STEP_CONFIRMATION;
        }

        $form_state->setRebuild();
        break;

      case self::STEP_CONFIRMATION:

        $triggeredElement = $form_state->getTriggeringElement();

        // Handle the clicking of remove buttons.
        if (mb_substr($triggeredElement['#name'], 0, 7) == 'remove-') {

          // Remove this legal entity from the list of selected legal entities.
          $ind = mb_substr($triggeredElement['#name'], 7);
          unset($state['selected_legal_entities'][$ind]);

          // If the last legal entity has been removed then go to first step so user can start again. Otherwise we
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
