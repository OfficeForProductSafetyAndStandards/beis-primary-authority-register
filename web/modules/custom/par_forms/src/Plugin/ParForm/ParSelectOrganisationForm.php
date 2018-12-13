<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\NestedArray;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Form for selecting an organisation.
 *
 * @ParForm(
 *   id = "organisation_select",
 *   title = @Translation("Organisation selection.")
 * )
 */
class ParSelectOrganisationForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $organisation_options = [];

    // Get the organisations that the current user belongs to.
    if ($this->getFlowDataHandler()->getCurrentUser()->isAuthenticated()) {
      $account = User::Load($this->getFlowDataHandler()->getCurrentUser()->id());
      $organisations = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_organisation', TRUE);
      $organisation_options = $this->getParDataManager()->getEntitiesAsOptions($organisations, []);
    }

    $this->getFlowDataHandler()->setFormPermValue('organisations', $organisation_options);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Get all the allowed authorities.
    $organisations = $this->getFlowDataHandler()->getFormPermValue('organisations');
    $required = $this->getFlowDataHandler()->getDefaultValues('organisation_required', TRUE);

    // If no suggestions were found cancel out of the journey.
    if ($required && count($organisations) <= 0) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getPrevRoute('prev'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    // If only one authority submit the form automatically and go to the next step.
    elseif ($required && count($organisations) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', key($organisations));
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    if ($organisations) {
      // Initialize pager and get current page.
      $number_of_items = 10;
      $current_page = pager_default_initialize(count($organisations), $number_of_items, $cardinality);

      // Split the items up into chunks:
      $chunks = array_chunk($organisations, $number_of_items, TRUE);

      $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE);
      $default_value = $this->getDefaultValuesByKey("par_data_organisation_id", $cardinality, NULL);
      $form['par_data_organisation_id'] = [
        '#type' => $multiple ? 'checkboxes' : 'radios',
        '#title' => t('Choose an Organisation'),
        '#options' => $organisations,
        '#default_value' => $multiple ? (array) $default_value : $default_value,
        '#attributes' => ['class' => ['form-group']],
      ];

      // @TODO Add pager so that any selected checkboxes aren't unselected when a new page is loaded.
//      $form['pager'] = [
//        '#type' => 'pager',
//        '#theme' => 'pagerer',
//        '#element' => $cardinality,
//        '#config' => [
//          'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
//        ],
//      ];
    }
    else {
      $form['intro'] = [
        '#type' => 'markup',
        '#markup' => "There are no organisations to choose from.",
        '#prefix' => '<p class=""form-group">',
        '#suffix' => '</p>',
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $required = $this->getFlowDataHandler()->getDefaultValues('organisation_required', TRUE);

    // If multiple choices are allowed the resulting value may be an array with keys but empty values.
    $organisation_element_key = $this->getElementKey('par_data_organisation_id', $cardinality);
    $authorities_selected = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE) ?
      NestedArray::filter((array) $form_state->getValue($organisation_element_key)) :
      $form_state->getValue($organisation_element_key);

    if ($required && empty($authorities_selected)) {
      $id_key = $this->getElementKey('par_data_organisation_id', $cardinality, TRUE);
      $form_state->setErrorByName($this->getElementName($organisation_element_key), $this->wrapErrorMessage('You must select an organisation.', $this->getElementId($id_key, $form)));
    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
