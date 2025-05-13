<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  #[\Override]
  public function loadData(int $index = 1): void {
    $organisation_options = [];

    // Get the organisations that the current user belongs to.
    if ($this->getFlowDataHandler()->getCurrentUser()->isAuthenticated()) {
      $account = User::Load($this->getFlowDataHandler()->getCurrentUser()->id());
      $organisations = $this->getParDataManager()->hasMembershipsByType($account, 'par_data_organisation', TRUE);
      $organisation_options = $this->getParDataManager()->getEntitiesAsOptions($organisations, []);
    }

    $this->getFlowDataHandler()->setFormPermValue('organisations', $organisation_options);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Get all the allowed authorities.
    $organisations = $this->getFlowDataHandler()->getFormPermValue('organisations');
    $required = $this->getFlowDataHandler()->getDefaultValues('organisation_required', TRUE);

    // If no suggestions were found cancel out of the journey.
    if ($required && count($organisations) <= 0) {
      $url = $this->getFlowNegotiator()->getFlow()->progress('cancel');
      return new RedirectResponse($url->toString());
    }

    // If only one authority submit the form automatically and go to the next step.
    elseif ($required && count($organisations) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_organisation_id', key($organisations));
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    if ($organisations) {
      // Initialize pager and get current page.
      $number_of_items = 10;
      $pager = $this->getUniquePager()->getPager('par_plugin_organisation_select_' . $index);
      $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($organisations), $number_of_items, $pager);

      // Split the items up into chunks:
      $chunks = array_chunk($organisations, $number_of_items, TRUE);
      $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];

      $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE);
      $default_value = $this->getDefaultValuesByKey("par_data_organisation_id", $index, NULL);
      $form['par_data_organisation_id'] = [
        '#type' => $multiple ? 'checkboxes' : 'radios',
        '#title' => t('Choose an Organisation'),
        '#title_tag' => 'h2',
        '#options' => $organisations,
        '#default_value' => $multiple ? (array) $default_value : $default_value,
        '#attributes' => ['class' => ['govuk-form-group']],
      ];

      // @todo Add pager so that any selected checkboxes aren't unselected when a new page is loaded.
      //   $form['pager'] = [
      //        '#type' => 'pager',
      //        '#theme' => 'pagerer',
      //        '#element' => $index,
      //        '#config' => [
      //          'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
      //        ],
      //      ];
    }
    else {
      $form['intro'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-form-group']],
        '#value' => $this->t('There are no organisations to choose from.'),
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $required = $this->getFlowDataHandler()->getDefaultValues('organisation_required', TRUE);

    // If multiple choices are allowed the resulting value may be an array with keys but empty values.
    $organisation_element_key = $this->getElementKey('par_data_organisation_id', $index);
    $organisations_selected = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', FALSE) ?
      NestedArray::filter((array) $form_state->getValue($organisation_element_key)) :
      $form_state->getValue($organisation_element_key);

    if ($required && empty($organisations_selected)) {
      $id_key = $this->getElementKey('par_data_organisation_id', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($organisation_element_key), $this->wrapErrorMessage('You must select an organisation.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
