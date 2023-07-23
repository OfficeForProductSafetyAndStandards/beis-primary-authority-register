<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\NestedArray;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Form for selecting a partnership.
 *
 * @ParForm(
 *   id = "partnership_select",
 *   title = @Translation("Partnership selection.")
 * )
 */
class ParSelectPartnershipForm extends ParFormPluginBase {

  const SELECT_ALL = 'all';

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $allow_multiple = $this->getConfiguration()['allow_multiple'] ?? FALSE;
    $this->getFlowDataHandler()->setFormPermValue("allow_multiple", (bool) $allow_multiple, $this);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $partnerships = $this->getFlowDataHandler()->getFormPermValue('partnerships');
    $partnership_options = $this->getParDataManager()->getEntitiesAsOptions($partnerships);
    $partnership_count = count($partnerships);

    // If no suggestions were found cancel out of the journey.
    if ($partnership_count <= 0) {
      $form['intro'] = [
        '#type' => 'markup',
        '#markup' => "There are no partnerships to choose from.",
        '#prefix' => '<p class=""form-group">',
        '#suffix' => '</p>',
      ];
    }
    // If only one partnership submit the form automatically and go to the next step.
    elseif ($partnership_count === 1) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_partnership_id', key($partnerships));
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }
    else {
      $multiple = $this->getFlowDataHandler()->getDefaultValues('allow_multiple', TRUE);

      if ($multiple) {
        $form['select_all'] = [
          '#type' => 'checkbox',
          '#title' => $this->t("Select all @count partnerships ", ['@count' => $partnership_count]),
          '#return_value' => self::SELECT_ALL,
          '#attributes' => ['class' => ['form-group']],
        ];
      }

      // Initialize pager and get current page.
      $number_of_items = 10;
      $pager_id = implode('_', ['par_plugin', $this->getPluginId(), $cardinality]);
      $pager = $this->getUniquePager()->getPager($pager_id);
      $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($partnerships), $number_of_items, $pager);

      // Split the items up into chunks:
      $chunks = array_chunk($partnership_options, $number_of_items, TRUE);
      $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];

      $default_value = $this->getDefaultValuesByKey("par_data_partnership_id", $cardinality, NULL);
      $form['par_data_partnership_id'] = [
        '#type' => $multiple ? 'checkboxes' : 'radios',
        '#title' => $multiple ? $this->t('Choose a partnerships') : $this->t('Choose partnerships'),
        '#options' => $partnership_options,
        '#default_value' => $multiple ? (array) $default_value : $default_value,
        '#attributes' => ['class' => ['form-group']],
      ];

      // If multiple selections are supported.
      if ($multiple) {
        $input_name = $this->getTargetName($this->getElementKey('select_all', $cardinality));
        $form['par_data_partnership_id']['#states'] = [
          'visible' => [
            ':input[name="'.$input_name.'"]' => ['checked' => FALSE],
          ],
        ];
      }

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

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $partnerships = $this->getFlowDataHandler()
      ->getFormPermValue('partnerships');
    $multiple = $this->getFlowDataHandler()
      ->getDefaultValues('allow_multiple', TRUE);

    $partnership_id_key = $this->getElementKey('par_data_partnership_id', $cardinality);

    // Populate partnership IDs if select all is checked.
    $select_all_key = $this->getElementKey('select_all', $cardinality);
    if ($form_state->getValue($select_all_key) === self::SELECT_ALL) {
      $form_state->setValue($partnership_id_key, array_keys($partnerships));
    }

    // If multiple choices are allowed the resulting value may be an array with keys but empty values.
    $partnership_ids = $multiple ?
      NestedArray::filter((array) $form_state->getValue($partnership_id_key)) :
      $form_state->getValue($partnership_id_key);

    if (empty($partnership_ids)) {
      $id_key = $this->getElementKey('par_data_partnership_id', $cardinality, TRUE);
      $form_state->setErrorByName($this->getElementName($partnership_id_key), $this->wrapErrorMessage('You must select a partnership.', $this->getElementId($id_key, $form)));
    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
