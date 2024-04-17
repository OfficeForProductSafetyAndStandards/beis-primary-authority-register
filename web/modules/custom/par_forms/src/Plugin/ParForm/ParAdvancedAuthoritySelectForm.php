<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\Controller\ParAutocompleteController;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Advanced selection mechanism for authorities.
 *
 * @ParForm(
 *   id = "advanced_authority_select",
 *   title = @Translation("Advanced authority selection.")
 * )
 */
class ParAdvancedAuthoritySelectForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    // Determine the selection mechanism based.
    if ($this->getFlowDataHandler()->getCurrentUser()->hasPermission('bypass par_data membership')) {
      $selection_mechanism = 'autocomplete';
    }
    else {
      $selection_mechanism = 'checkboxes';
    }
    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $form['authority'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the new authority'),
      '#description' => $this->t('Once you enter the name of an authority you will be able to choose which partnerships you would like to migrate.'),
      '#autocomplete_route_name' => 'par_forms.autocomplete',
      '#autocomplete_route_parameters' => [
        'plugin' => $this->getPluginId(),
      ],
      '#autocomplete_query_parameters' => [
        'target_type' => 'par_data_authority',
      ],
    ];

    // $form['intro'] = [
    //        '#type' => 'markup',
    //        '#markup' => "There are no authorities to choose from.",
    //        '#prefix' => '<p class=""form-group">',
    //        '#suffix' => '</p>',
    //      ];
    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $authority_id_key = $this->getElementKey('authority', $index);

    $authority = $form_state->getValue($authority_id_key);
    $authority_id = ParAutocompleteController::extractEntityIdFromAutocompleteInput($authority);

    if (!empty($authority_id)) {
      $form_state->setValue('authority_id', (int) $authority_id);
    }
    else {
      $id_key = $this->getElementKey('authority', $index, TRUE);
      $form_state->setErrorByName('authority', $this->wrapErrorMessage('You must select an authority.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
