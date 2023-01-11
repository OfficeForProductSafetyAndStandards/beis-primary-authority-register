<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Render\Markup;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\registered_organisations\OrganisationProfile;

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

    // First time in.
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
        $state['existing_legal_entities'][] = new OrganisationProfile([
          'register' => $existing_legal_entity->getRegistry(),
          'type' => $existing_legal_entity->getTypeRaw(),
          'id' =>  $existing_legal_entity->getRegisteredNumber(),
          'name' => $existing_legal_entity->getName(),
        ]);
      }
    }

    switch ($state['step']) {

      case self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY:

        // There are existing entities that can be selected.
        if (!empty($state['existing_legal_entities'])) {

          // Add the checkboxes control.
          $form['existing_legal_entities'] = [
            '#type' => 'checkboxes',
            '#title' => 'Choose from the organisation\'s existing legal entities',
            '#description' => '<p>Select legal entities that are already attached to the organisation but are not part of the partnership.</p>',
            '#options' => [],
            '#attributes' => ['class' => ['form-group']],
          ];

          // Add options and descriptions.
          /* @var OrganisationProfile $existing_legal_entity */
          foreach ($state['existing_legal_entities'] as $ind => $existing_legal_entity) {
            $form['existing_legal_entities']['#options'][$ind] = $existing_legal_entity->getName();
            $parts = [];
            $parts[] = $existing_legal_entity->getType();
            $parts[] = $existing_legal_entity->getId();
            $parts = array_filter($parts);
            $form['existing_legal_entities'][$ind]['#description'] = implode(' - ', $parts);
          }

          // Add the 'add other' option
          $form['existing_legal_entities']['#options']['add_other'] = 'No, I want to add a different legal entity';
          $form['existing_legal_entities']['add_other']['#prefix'] = Markup::create('<div class="govuk-checkboxes__divider">or</div>');

          $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Continue');
          break;
        }

        // There are no existing legal entities to select from. We drop through to the next step.
        $state['step'] = self::STEP_CHOOSE_REGISTRY;

      case self::STEP_CHOOSE_REGISTRY:

        // Get array plugin definitions and move internal plugin to end of array.
        /* @var \Drupal\registered_organisations\OrganisationManager $om */
        $om = \Drupal::service('registered_organisations.organisation_manager');
        $plugins = $om->getDefinitions();
        if (isset($plugins['internal'])) {
          $tmp = $plugins['internal'];
          unset($plugins['internal']);
          $plugins['internal'] = $tmp;
        }

        // Create the registry radio control.
        $form['registry'] = [
          '#type' => 'radios',
          '#title' => 'What type of legal entity do you want to add?',
          '#options' => [],
        ];

        // Add options and descriptions.
        foreach ($plugins as $ind => $plugin) {
          $form['registry']['#options'][$ind] = $plugin['label'];
          $form['registry'][$ind]['#description'] = $plugin['description'];
        }

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
          '#options' => [],
          '#required' => TRUE,
        ];

        /* @var \Drupal\registered_organisations\OrganisationManager $om */
        $om = \Drupal::service('registered_organisations.organisation_manager');
        $class = $om->getDefinition('internal')['class'];

        foreach (array_keys($class::ORGANISATION_TYPE) as $key) {
          $form['legal_entity_type']['#options'][$key] = $class::ORGANISATION_TYPE[$key];
          $form['legal_entity_type'][$key]['#description'] = $class::ORGANISATION_TYPE_DESC[$key];
        }

        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Continue');

        break;

      case self::STEP_REGISTRY_SEARCH:

        // We have search results.
        if (!empty($state['search_results'])) {

          // Get the plugin
          /* @var \Drupal\registered_organisations\OrganisationManager $om */
          $om = \Drupal::service('registered_organisations.organisation_manager');
          $plugin = $om->createInstance($state['registry']);

          // Add checkboxes control to display found organisations for selection.
          $form['search_results'] = [
            '#type' => 'checkboxes',
            '#title' => 'Choose legal entities',
            '#description' => 'We have found ' . count($state['search_results']) . ' matching legal entities in the register.',
            '#options' => [],
          ];

          // Add options and description to the checkboxes control.
          /* @var OrganisationProfile $search_result */
          foreach ($state['search_results'] as $ind => $search_result) {
            $form['search_results']['#options'][$ind] = $search_result->getName();
            $parts = [];
            $parts[] = $search_result->getType();
            $parts[] = $search_result->getId();
            $parts = array_filter($parts);
            $form['search_results'][$ind]['#description'] = implode(' - ', $parts);
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

        $label = (empty($state['search_results'])) ? 'Search' : 'Search again';
        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle($label);

        break;

      case self::STEP_CONFIRMATION:

        $form['confirm_entities'] = [
          '#type' => 'table',
          '#header' => [
            'Legal entity',
            'Action',
          ],
        ];

        // Add a row for each selected legal entity.
        /* @var OrganisationProfile $selected_legal_entity */
        foreach ($state['selected_legal_entities'] as $ind => $selected_legal_entity) {

          $name = $selected_legal_entity->getName();
          $parts = [];
          $parts[] = $selected_legal_entity->getType();
          $parts[] = $selected_legal_entity->getId();
          $parts = array_filter($parts);
          $type_number = implode(' - ', $parts);

          $form['confirm_entities'][$ind]['legal_entity'] = [
            '#type' => 'container',
            '#attributes' => ['class' => 'column-full'],
            'name' => [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => $name,
            ],
            'type_number' => [
              '#type' => 'html_tag',
              '#tag' => 'div',
              '#value' => $type_number,
            ],
          ];

          $form['confirm_entities'][$ind]['operations'] = [
            '#type' => 'container',
            'remove' => [
              '#type' => 'button',
              '#name' => 'remove-' . $ind,
              '#value' => 'remove',
              '#attributes' => ['class' =>['btn-link']]
            ],
          ];
        }

        // Add 'select another' button.
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

        if ($state['registry'] == 'internal') {
          $state['step'] = self::STEP_UNREGISTERED_LEGAL_ENTITY_ENTRY;
        }
        else {
          $state['step'] = self::STEP_REGISTRY_SEARCH;
        }

        $form_state->setRebuild();
        break;

      case self::STEP_UNREGISTERED_LEGAL_ENTITY_ENTRY:

        $state['selected_legal_entities'][] = new OrganisationProfile([
          'register' => 'internal',
          'type' => $form_state->getValue('legal_entity_type'),
          'id' =>  NULL,
          'name' => $form_state->getValue('legal_entity_name'),
        ]);

        $state['step'] = self::STEP_CONFIRMATION;
        $form_state->setRebuild();

        break;

      case self::STEP_REGISTRY_SEARCH:

        $triggeredElement = $form_state->getTriggeringElement();

        // Handle selection from results..
        if ($triggeredElement['#name'] == 'choose_submit') {

          // Get chosen search results, if any.
          $chosen = array_keys(array_filter($form_state->getValue('search_results', []), function($val) {
            return is_string($val);
          }));
/*          $search_results = $form_state->getValue('search_results', []);
          foreach ($search_results as $key => $val) {
            if (is_string($val)) {
              $chosen[] = $key;
            }
          }*/

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
        $def = $om->getDefinition($state['registry']);
        $results = $om->searchOrganisation($state['search_term'], $def);

        $state['search_results'] = [];
        foreach ($results as $result) {

          // @todo Filter out LEs that we already have in our selected list or are already attached to the org.

          $state['search_results'][] = $result;
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
          break;
        }

        // User has confirmed the chosen legal entities.
        // Copy legal entities to metadata for use by next step in flow and clear our state.
        $this->getFlowDataHandler()->setMetaDataValue('legal_entities_to_add', $state['selected_legal_entities']);
        $state = [];

        break;

      default:
        throw new ParFlowException('Invalid step ' . $state['step'] . ' in ' . __CLASS__ . '::' . __METHOD__ . '.');
    }

    // Save plugin state.
    $this->getFlowDataHandler()->setMetaDataValue($this->getPluginId() . ':state', $state);
  }
}
