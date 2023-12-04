<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\par_forms\Controller\ParAutocompleteController;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Utility\Html;

/**
 * Form for selecting memberships available to the current user.
 *
 * @ParForm(
 *   id = "memberships_select",
 *   title = @Translation("Membership selection.")
 * )
 */
class ParSelectMembershipsForm extends ParFormPluginBase {

  /**
   * Const for the advanced element limit.
   */
  const ADVANCED_SELECTION_LIMIT = 10;

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $user_organisations = [];
    $user_authorities = [];

    // If the person being edited has an ordinary user account get all the user's
    // memberships, otherwise use the person being edited if there is one.
    if ($account = $this->getFlowDataHandler()->getParameter('user')) {
      $memberships = $this->getParDataManager()->hasMemberships($account, TRUE);

      $user_organisations = array_filter($memberships, function ($membership) {
        return ('par_data_organisation' === $membership->getEntityTypeId());
      });
      $user_authorities = array_filter($memberships, function ($membership) {
        return ('par_data_authority' === $membership->getEntityTypeId());
      });
    }
    else if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      // Get the directly related authorities and organisations.
      $par_relationship_manager = $this->getParDataManager()->getReducedIterator(1);
      $memberships = $par_relationship_manager->getRelatedEntities($par_data_person);

      $user_organisations = array_filter($memberships, function ($membership) {
        return ('par_data_organisation' === $membership->getEntityTypeId());
      });
      $user_authorities = array_filter($memberships, function ($membership) {
        return ('par_data_authority' === $membership->getEntityTypeId());
      });
    }

    // If the user has permissions to select from all memberships.
    if ($this->getFlowNegotiator()->getCurrentUser()->hasPermission('assign all memberships')) {
      $organisation_options = $this->getParDataManager()->getEntitiesByProperty('par_data_organisation', 'deleted', 0);
      $authority_options = $this->getParDataManager()->getEntitiesByProperty('par_data_authority', 'deleted', 0);
    }
    else {
      // Get the memberships for the current user.
      $current_user = $this->getFlowNegotiator()->getCurrentUser();
      $memberships = $this->getParDataManager()
        ->hasMemberships($current_user, TRUE);

      $organisation_options = array_filter($memberships, function ($membership) {
        return ('par_data_organisation' === $membership->getEntityTypeId());
      });
      $authority_options = array_filter($memberships, function ($membership) {
        return ('par_data_authority' === $membership->getEntityTypeId());
      });
    }

    // Store the existing organisation memberships
    $organisation_ids = [];
    foreach ($user_organisations as $user_organisation) {
      $organisation_ids[] = $user_organisation->id();
    }
    // Transform the default values into the autocomplete format.
    if ($organisation_options > self::ADVANCED_SELECTION_LIMIT) {
      $organisation_entities = !empty(array_filter($organisation_ids)) ? $this->getParDataManager()
        ->getEntitiesByType('par_data_organisation', $organisation_ids) : [];
      $organisation_ids = $this->getParDataManager()->getEntitiesAsAutocomplete($organisation_entities);
    }
    $this->getFlowDataHandler()->setFormPermValue('par_data_organisation_id', $organisation_ids);

    // Store the existing authority memberships.
    $authority_ids = [];
    foreach ($user_authorities as $user_authority) {
      $authority_ids[] = $user_authority->id();
    }
    $this->getFlowDataHandler()->setFormPermValue('par_data_authority_id', $authority_ids);

    $this->getFlowDataHandler()->setFormPermValue('organisation_options', $this->getParDataManager()->getEntitiesAsOptions($organisation_options, []));
    $this->getFlowDataHandler()->setFormPermValue('authority_options', $this->getParDataManager()->getEntitiesAsOptions($authority_options, []));

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed organisations and authorities.
    $organisation_options = $this->getFlowDataHandler()->getFormPermValue('organisation_options');
    $authority_options = $this->getFlowDataHandler()->getFormPermValue('authority_options');

    // Should the user be able to select multiple options.
    $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE);

    $elements = [
      'par_data_authority' => $authority_options,
      'par_data_organisation' => $organisation_options,
    ];
    $i=0;
    foreach ($elements as $target_type => $options) {
      $element_weight = $i*10;
      $element_key = "{$target_type}_id";
      $default_value = $this->getDefaultValuesByKey($element_key, $index, NULL);

      // Do not render an input element if there are no options to select.
      if (empty($options)) {
        continue;
      }

      switch ($target_type) {
        case 'par_data_authority':
          $element_title = $this->t('Which authorities are they a member of?');
          break;

        case 'par_data_organisation':
          $element_title = $this->t('Which organisations are they a member of?');
          break;

      }

      // Get the base for all elements.
      $base = [
        '#title' => $element_title ?? '',
        '#attributes' => ['class' => ['govuk-form-group']],
        '#weight' => $element_weight
      ];

      // Identify the type of selection interface that should be used.
      if (count($options) > self::ADVANCED_SELECTION_LIMIT) {
        // Transform the default values into the autocomplete format.
        $authority_ids = array_filter($default_value);
        $authority_entities = !empty($authority_ids) ? $this->getParDataManager()
          ->getEntitiesByType($target_type, $authority_ids) : [];
        $authority_values = $this->getParDataManager()->getEntitiesAsAutocomplete($authority_entities);

        // The par autocomplete element.
        $form[$element_key] = $base + [
          '#type' => 'textfield',
          '#description' => $this->t('If you need to enter multiple values please separate them with a comma.'),
          '#default_value' => $authority_values,
          '#autocomplete_route_name' => 'par_forms.autocomplete',
          '#autocomplete_route_parameters' => [
            'plugin' => $this->getPluginId(),
          ],
          '#autocomplete_query_parameters' => [
            'target_type' => $target_type,
          ],
          '#multiple' => $multiple ? TRUE : FALSE
        ];

      }
      elseif ($multiple) {
        $form[$element_key] = $base + [
          '#type' => 'checkboxes',
          '#options' => $options,
          '#default_value' => (array) $default_value,
        ];
      }
      else {
        $form[$element_key] = $base + [
          '#type' => 'radios',
          '#options' => $options,
          '#default_value' => $default_value,
        ];
      }

      // Increment the element weight.
      $i++;
    }

    if (empty($organisation_options) && empty($authority_options)) {
      $form['intro'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-form-group']],
        '#value' => $this->t('There are no memberships to select from.'),
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    // Allow validation to be disabled if memberships are not required.
    $required = $this->getFlowDataHandler()->getDefaultValues('required', TRUE);
    // Whether multiple values are allowed.
    $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE);

    // Get all the allowed organisations and authorities.
    $authority_options = $this->getFlowDataHandler()
      ->getFormPermValue('authority_options');
    $organisation_options = $this->getFlowDataHandler()
      ->getFormPermValue('organisation_options');

    // Transform autocomplete values back into IDs.
    $elements = [
      'par_data_authority' => $authority_options,
      'par_data_organisation' => $organisation_options,
    ];
    $selected = [];
    $i = 0;
    foreach ($elements as $target_type => $options) {
      $element_key = "{$target_type}_id";
      $element = $this->getElementKey($element_key, $index);

      // Do not render an input element if there are no options to select.
      if (empty($options)) {
        $selected[$target_type] = [];
        continue;
      }

      // Identify the type of selection interface that should be used.
      if (count($options) > self::ADVANCED_SELECTION_LIMIT) {
        $values = $form_state->getValue($element);
        $selected[$target_type] = array_map('intval', ParAutocompleteController::extractEntityIdsFromAutocompleteInput($values));
        $form_state->setValue($element, $selected[$target_type]);
      }
      // If multiple choices are allowed the resulting value may be an array with keys but empty values.
      else {
        $selected[$target_type] = $multiple ?
          NestedArray::filter((array) $form_state->getValue($element)) :
          $form_state->getValue($element);
      }
    }

    // One of either the authorities of the organisations must be selected.
    if ($required &&
      (empty($authority_options) || empty($selected['par_data_authority'])) &&
      (empty($organisation_options) || empty($selected['par_data_organisation']))) {
      $authority_id_key = $this->getElementKey("par_data_authority_id", $index, TRUE);
      $organisation_id_key = $this->getElementKey("par_data_organisation_id", $index, TRUE);
      $form_state->setErrorByName(
        $this->getElementName($element),
        $this->wrapErrorMessage('You must add this person to at least one organisation or authority.',
          $this->getElementId($authority_id_key, $form)
        )
      );
      $form_state->setErrorByName(
        $this->getElementName($element),
        $this->wrapErrorMessage('You must add this person to at least one organisation or authority.',
          $this->getElementId($organisation_id_key, $form)
        )
      );
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
