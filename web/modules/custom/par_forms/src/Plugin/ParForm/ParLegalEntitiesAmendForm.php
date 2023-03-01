<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Render\Markup;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\registered_organisations\OrganisationProfile;

/**
 * Legal entity select form plugin.
 *
 * @ParForm(
 *   id = "legal_entities_amend",
 *   title = @Translation("Legal entities amend form.")
 * )
 */
class ParLegalEntitiesAmendForm extends ParFormPluginBase {

  const STEP_LIST_LEGAL_ENTITIES = 1;
  const STEP_CHOOSE_EXISTING_LEGAL_ENTITY = 2;
  const STEP_CHOOSE_REGISTRY = 3;
  const STEP_UNREGISTERED_LEGAL_ENTITY_ENTRY = 4;
  const STEP_REGISTRY_SEARCH = 5;

  /**
   * {@inheritdoc}
   */
  protected $wrapperName = 'legal entities amend';

  /**
   * @defaults
   */
  protected $formDefaults = [
  ];

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    // Get the config defined in flow yml file.
    $config = $this->getConfiguration();

    // Get the plugin state.
    $state = $this->getFlowDataHandler()->getMetaDataValue($this->getPluginId() . ':state');

    // Get the partnership and organisation.
    /* @var \Drupal\par_data\Entity\ParDataPartnership $partnership */
    $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $organisation = $partnership->getOrganisation(TRUE);

    // Get the amendment PLEs in required state.
    $amend_partnership_legal_entities = $partnership->getPartnershipLegalEntities(FALSE, 'awaiting_review');

    // First time in.
    if (empty($state['step'])) {

      // Initialise state.
      $state['step'] = self::STEP_LIST_LEGAL_ENTITIES;
      $state['existing_legal_entities'] = [];
      $state['selected_legal_entities'] = [];
      $state['search_results'] = [];
      $state['search_term'] = '';
    }

    switch ($state['step']) {

      case self::STEP_LIST_LEGAL_ENTITIES:

        // If there are legal entities or mode is display show the table of LEs.
        if (!empty($amend_partnership_legal_entities) || $config['edit'] == FALSE) {

          $form['legal_entities'] = [
            '#type' => 'table',
            '#header' => [
              'Legal entity',
              'Action',
            ],
          ];
          if ($config['edit'] == TRUE) {
            $form['confirm_entities']['#header'][] = 'Action';
          }

          // Add a row for each PLE.
          foreach ($amend_partnership_legal_entities as $amend_partnership_legal_entity) {

            $id = $amend_partnership_legal_entity->id();

            $legal_entity = $amend_partnership_legal_entity->getLegalEntity();

            $name = $legal_entity->getName();
            $parts = [];
            $parts[] = $legal_entity->getType();
            $parts[] = $legal_entity->getRegisteredNumber();
            $parts = array_filter($parts);
            $type_number = implode(' - ', $parts);

            $form['legal_entities'][$id]['legal_entity'] = [
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

            if ($config['edit'] == TRUE) {
              $form['legal_entities'][$id]['operations'] = [
                '#type' => 'container',
                'remove' => [
                  '#type' => 'button',
                  '#name' => 'remove-' . $id,
                  '#value' => 'remove',
                  '#attributes' => ['class' => ['btn-link']]
                ],
              ];
            }
          }

          // Add 'select another' button.
          if ($config['edit'] == TRUE) {
            $form['select_another'] = [
              '#type' => 'button',
              '#name' => 'select-another',
              '#value' => 'select another legal entity',
              '#attributes' => ['class' => ['btn-link']]
            ];
          }

          $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Confirm');

          break;
        }

        // Drop through to choose from existing unattached LEs on the organisation.
        $state['step'] = self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY;

      case self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY:

        // Get the set of list of legal entities already attached to the organisation.
        $organisation_legal_entities = !empty($organisation) ? $organisation->getLegalEntity() : [];

        // Remove legal entities already attached to the partnership.
        $partnership_legal_entities = $partnership->getLegalEntity();
        foreach ($organisation_legal_entities as $key => $organisation_legal_entity) {
          foreach ($partnership_legal_entities as $partnership_legal_entity) {
            if ($organisation_legal_entity === $partnership_legal_entity) {
              unset($organisation_legal_entity[$key]);
              break;
            }
          }
        }

        // Remove legal entities already included in this amendment.
        foreach ($organisation_legal_entities as $key => $organisation_legal_entity) {
          foreach ($amend_partnership_legal_entities as $amend_partnership_legal_entity) {
            if ($organisation_legal_entity === $amend_partnership_legal_entity) {
              unset($organisation_legal_entity[$key]);
              break;
            }
          }
        }

        // There are existing entities that can be selected.
        if (!empty($organisation_legal_entities)) {

          // Add the checkboxes control.
          $form['existing_legal_entities'] = [
            '#type' => 'checkboxes',
            '#title' => 'Choose from the organisation\'s existing legal entities',
            '#description' => 'Select legal entities that are already attached to the organisation but are not part of the partnership.',
            '#options' => [],
            '#attributes' => ['class' => ['form-group']],
          ];

          // Add options and descriptions.
          foreach ($organisation_legal_entities as $organisation_legal_entity) {
            $id = $organisation_legal_entity->id();
            $form['existing_legal_entities']['#options'][$id] = $organisation_legal_entity->getName();
            $parts = [];
            $parts[] = $organisation_legal_entity->getType();
            $parts[] = $organisation_legal_entity->getRegisteredNumber();
            $parts = array_filter($parts);
            $form['existing_legal_entities'][$id]['#description'] = implode(' - ', $parts);
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
          '#title' => 'With which authority is the legal entity registered?',
          '#description' => 'Choose the organisation where the legal entity is registered. ' .
                                     'For unregistered legal entities choose \'Unregistered\'.',
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
          '#title' => '<h1 class="heading-medium">Name of the legal entity</h1>',
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

        // Get the plugin definition.
        /* @var \Drupal\registered_organisations\OrganisationManager $om */
        $om = \Drupal::service('registered_organisations.organisation_manager');
        $def = $om->getDefinition($state['registry']);

        // We have search results.
        if (!empty($state['search_results'])) {

          // Add checkboxes control to display found organisations for selection.
          $form['search_results'] = [
            '#type' => 'checkboxes',
            '#title' => 'Choose legal entities',
            '#description' => 'We have found ' . count($state['search_results']) . ' results matching legal entities ' .
                              'in the ' . $def['label'] . ' register.',
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

        // The search fields.
        $form['search'] = [
          '#type' => 'fieldset',
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'h2',
            '#value' => 'Search the ' . $def['label'] . ' register',
            '#attributes' => ['class' => ['heading-medium']],
          ],
        ];

        $form['search']['search_term'] = [
          '#type' => 'textfield',
          '#title' => 'Enter the terms to be searched for',
          '#default_value' => $state['search_term'],
          '#required' => empty($state['search_results']),
        ];

        $form['search']['search_help'] = [
          '#type' => 'details',
          '#title' => 'Help with searching',
        ];

        $form['search']['search_help']['advice'] = [
          '#type' => 'container',
          'p-1' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => 'The search of the register tries to match all the terms entered and returns the first 20 ' .
                        'results. If you are having difficulty finding the legal entity try variations of spelling ' .
                        'or more specific terms.',
          ],
          'p-2' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => 'We only search for active legal entities. If the legal entity is closed or in ' .
                        'receivership it will not be found.',
          ],
          'p-3' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => 'The <a href="' . $def['url'] . '">' . $def['label'] . ' registry</a> provides a ' .
                        '<a href="' . $def['search_url'] . '">general registry search</a>. This ' .
                        'has many more search options, and can search both active and inactive legal entities.',
          ],
        ];

        $label = (empty($state['search_results'])) ? 'Search' : 'Search again';
        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle($label);

        break;

      default:
        throw new ParFlowException('Invalid step ' . $state['step'] . ' in ' . __CLASS__ . '::' . __METHOD__ . '.');
    }

    // Save plugin state.
    $this->getFlowDataHandler()->setMetaDataValue($this->getPluginId() . ':state', $state);

    return $form;
  }

  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {

    parent::validate($form, $form_state, $cardinality, $action);

    $state = $this->getFlowDataHandler()->getMetaDataValue($this->getPluginId() . ':state');

    /* @var \Drupal\par_data\Entity\ParDataPartnership $partnership */
    $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $organisation = $partnership->getOrganisation(TRUE);

    switch ($state['step']) {

      case self::STEP_LIST_LEGAL_ENTITIES:

        $triggeredElement = $form_state->getTriggeringElement();

        // Handle the clicking of remove buttons.
        if (mb_substr($triggeredElement['#name'], 0, 7) == 'remove-') {

          // Remove this legal entity from amend legal entities set.
          $id = mb_substr($triggeredElement['#name'], 7);
          $partner_legal_entity = \Drupal::entityTypeManager()->getStorage('par_data_partnership_le')->load($id);
          $partner_legal_entity->delete();

          // Redisplay this step to view remaining amendment LEs or drop through to select new ones if none left.
          $form_state->setRebuild();
          break;
        }

        // Handle 'add another' button.
        if ($triggeredElement['#name'] == 'select-another') {
          $state['step'] = self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY;
          $form_state->setRebuild();
          break;
        }

        break;

      case self::STEP_CHOOSE_EXISTING_LEGAL_ENTITY:

        // Get chosen legal entities, if any.
        $chosen = array_filter($form_state->getValue('existing_legal_entities', []), function($val) {
          return is_string($val);
        });

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

        // User has selected at least one of the existing legal entities. Add to the amendment set.
        foreach (array_keys($chosen) as $id) {
          $legal_entity = \Drupal::entityTypeManager()->getStorage('par_data_legal_entity')->load($id);
          $partnership->addLegalEntity($legal_entity, NULL, NULL, 'awaiting_review');
        }

        // Send the user back to the list step.
        $state['step'] = self::STEP_LIST_LEGAL_ENTITIES;
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

        // Get existing or create new LE.
        $legalEntity = ParDataLegalEntity::create([
          'registry' => 'internal',
          'legal_entity_type' => $form_state->getValue('legal_entity_type'),
          'registered_name' => $form_state->getValue('legal_entity_name'),
          'registered_number' => NULL,
          'name' => $form_state->getValue('legal_entity_name'),
        ]);

        // If new we need to add LE to the organisation.
        if ($legalEntity->isNew()) {
          $legalEntity->save();
          $organisation->addLegalEntity($legalEntity);
        }

        // If existing then we need to check that it belongs to our organisation.
        else {
          if (!$organisation->hasLegalEntity($legalEntity)) {
            $id = $this->getElementId(['legal_entity_name'], $form);
            $form_state->setErrorByName(
              $this->getElementName('legal_entity_name'),
              $this->wrapErrorMessage(
                $legalEntity->getName() .
                ' already belongs to some other organisation, you may not add it to ' .
                $organisation->getName() . '.', $id));
          }
        }

        // Now add the legal entity to the partnership.
        $partnership->addLegalEntity($legalEntity);

        // Send user back to the list step.
        $state['step'] = self::STEP_LIST_LEGAL_ENTITIES;
        $form_state->setRebuild();

        break;

      case self::STEP_REGISTRY_SEARCH:

        $triggeredElement = $form_state->getTriggeringElement();

        // Handle selection from results..
        if ($triggeredElement['#name'] == 'choose_submit') {

          // Get chosen search results, if any.
          $chosen = array_filter($form_state->getValue('search_results', []), function($val) {
            return is_string($val);
          });

          // Error if nothing chosen.
          if (empty($chosen)) {
            $id = $this->getElementId(['search_results'], $form);
            $form_state->setErrorByName($this->getElementName('search_results'), $this->wrapErrorMessage('Please choose one of the options.', $id));
            break;
          }

          // Process chosen LEs.
          foreach (array_keys($chosen) as $ind) {

            /* @var OrganisationProfile $op */
            $op = $state['search_results'][$ind];

            // Get existing or create new LE.
            $legalEntity = ParDataLegalEntity::create([
              'registry' => $op->getRegister(),
              'legal_entity_type' => $op->getTypeRaw(),
              'registered_name' => $op->getName(),
              'registered_number' => $op->getId(),
              'name' => $op->getName(),
            ]);

            // If new we need to add LE to the organisation.
            if ($legalEntity->isNew()) {
              $legalEntity->save();
              $organisation->addLegalEntity($legalEntity);
            }

            // If existing then we need to check that it belongs to our organisation.
            else {
              if ($organisation->hasLegalEntity($legalEntity)) {
                $id = $this->getElementId(['search_results'], $form);
                $form_state->setErrorByName(
                  $this->getElementName('search_results'),
                  $this->wrapErrorMessage(
                    $legalEntity->getName() .
                    ' already belongs to some other organisation, you may not add it to ' .
                    $organisation->getName() . '.', $id));
              }
            }

            // Now add the legal entity to the partnership.
            $partnership->addLegalEntity($legalEntity);
          }

          // Save organisation and partnership changes.
          $organisation->save();
          $partnership->save();

          // Clear search term and results in case user goes around again.
          $state['search_term'] = '';
          $state['search_results'] = [];

          // Go back to the list step.
          $state['step'] = self::STEP_LIST_LEGAL_ENTITIES;
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

        if (empty($state['search_results'])) {
          $id = $this->getElementId(['search_term'], $form);
          $form_state->setErrorByName($this->getElementName('search_term'), $this->wrapErrorMessage('No active charities found for the given search term.', $id));
          break;
        }

        $form_state->setRebuild();
        break;

      default:
        throw new ParFlowException('Invalid step ' . $state['step'] . ' in ' . __CLASS__ . '::' . __METHOD__ . '.');
    }

    // Save step and plugin state.
    $this->getFlowDataHandler()->setMetaDataValue($this->getPluginId() . ':state', $state);
  }
}
