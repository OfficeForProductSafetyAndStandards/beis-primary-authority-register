<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_forms\Controller\ParAutocompleteController;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
  public function loadData($cardinality = 1) {
    // Determine the selection mechanism based
    if ($this->getFlowDataHandler()->getCurrentUser()->hasPermission('bypass par_data membership')) {
      $selection_mechanism = 'autocomplete';
    }
    else {
      $selection_mechanism = 'checkboxes';
    }
    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $form['authority'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the new authority'),
      '#description' => $this->t('Once you enter the name of an authority you will be able to choose which partnerships you would like to migrate.'),
      '#autocomplete_route_name' => 'par_forms.autocomplete',
      '#autocomplete_route_parameters' => [
        'plugin' => $this->getPluginId(),
      ],
    ];

//      $form['intro'] = [
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
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $authority_id_key = $this->getElementKey('authority', $cardinality);

    $authority = $form_state->getValue($authority_id_key);
    $authority_id = ParAutocompleteController::extractEntityIdFromAutocompleteInput($authority);

    if (!empty($authority_id)) {
      $form_state->setValue('authority_id', (int) $authority_id);
    }
    else {
      $id_key = $this->getElementKey('authority', $cardinality, TRUE);
      $form_state->setErrorByName('authority', $this->wrapErrorMessage('You must select an authority.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $cardinality, $action);
  }
}
