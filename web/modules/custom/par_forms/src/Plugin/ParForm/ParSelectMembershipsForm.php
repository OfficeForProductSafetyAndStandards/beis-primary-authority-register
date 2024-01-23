<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_forms\Controller\ParAutocompleteController;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\par_roles\ParRoleManagerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
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
   * Get the PAR Role manager.
   */
  protected function getParRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    // Get the user account.
    $account = $this->getFlowDataHandler()->getParameter('user');
    // Get the person.
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    // Try to get the account from the person.
    if (!$account && $par_data_person instanceof ParDataPersonInterface) {
      $account = $par_data_person->getUserAccount();
    }

    // Get the current memberships from the user account if there is one.
    if ($account instanceof UserInterface) {
      $user_authorities = iterator_to_array($this->getParRoleManager()->getInstitutions($account, 'par_data_authority'));
      $user_organisations = iterator_to_array($this->getParRoleManager()->getInstitutions($account, 'par_data_organisation'));
    }
    // Otherwise try to get the current memberships from the person.
    else if (!$account && $par_data_person instanceof ParDataPersonInterface) {
      $user_authorities = iterator_to_array($par_data_person->getInstitutions('par_data_authority'));
      $user_organisations = iterator_to_array($par_data_person->getInstitutions('par_data_organisation'));
    }

    // Provide all institutions as options if the current user has the correct permissions.
    if ($this->getFlowNegotiator()->getCurrentUser()->hasPermission('assign all memberships')) {
      $authorities = $this->getParDataManager()->getEntitiesByProperty('par_data_authority', 'deleted', 0);
      $organisations = $this->getParDataManager()->getEntitiesByProperty('par_data_organisation', 'deleted', 0);
    }
    // Otherwise only allow the current user to assign memberships that they also have.
    else {
      // Get the memberships for the current user.
      $current_user = $this->getFlowNegotiator()->getCurrentUser();

      $authorities = iterator_to_array($this->getParRoleManager()->getInstitutions($current_user, 'par_data_authority'));
      $organisations = iterator_to_array($this->getParRoleManager()->getInstitutions($current_user, 'par_data_organisation'));
    }

    // Get the user's authority and organisation as an array of IDs.
    $authority_ids = array_keys($this->getParDataManager()->getEntitiesAsOptions($user_authorities ?? []));
    $organisation_ids = array_keys($this->getParDataManager()->getEntitiesAsOptions($user_organisations ?? []));

    // Get the authorities and organisations as options.
    $authority_options = $this->getParDataManager()->getEntitiesAsOptions($authorities);
    $organisation_options = $this->getParDataManager()->getEntitiesAsOptions($organisations);

    // Set authority information,
    $this->getFlowDataHandler()->setFormPermValue('par_data_authority_id', $authority_ids);
    $this->getFlowDataHandler()->setFormPermValue('par_data_authority_original', $authority_ids);
    $this->getFlowDataHandler()->setFormPermValue('authority_options', $authority_options);

    // Set organisation information.
    $this->getFlowDataHandler()->setFormPermValue('par_data_organisation_id', $organisation_ids);
    $this->getFlowDataHandler()->setFormPermValue('par_data_organisation_original', $organisation_ids);
    $this->getFlowDataHandler()->setFormPermValue('organisation_options', $organisation_options);

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
      $default_value = $this->getDefaultValuesByKey($element_key, $index, []);

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

      // Render a hidden element to store the user's original memberships,
      // this helps to identify which memberships the current user cannot modify.
      $form["{$target_type}_original"] = [
        '#type' => 'hidden',
        '#value' => $this->getDefaultValuesByKey("{$target_type}_original", $index, []),
      ];

      // Get the base for all elements.
      $base = [
        '#title' => $element_title ?? '',
        '#title_tag' => 'h2',
        '#attributes' => ['class' => ['govuk-form-group']],
        '#weight' => $element_weight
      ];

      // Identify the type of selection interface that should be used.
      if (count($options) > self::ADVANCED_SELECTION_LIMIT) {
        // Transform the default values into the autocomplete format.
        $entity_ids = array_filter($default_value);
        $entities = !empty($entity_ids) ? $this->getParDataManager()
          ->getEntitiesByType($target_type, $entity_ids) : [];
        $default_values = $this->getParDataManager()->getEntitiesAsAutocomplete($entities);
        $default_value = implode(', ', $default_values);

        // The par autocomplete element.
        $form[$element_key] = $base + [
          '#type' => 'textfield',
          '#description' => $this->t('If you need to enter multiple values please separate them with a comma.'),
          '#default_value' => $default_value,
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
