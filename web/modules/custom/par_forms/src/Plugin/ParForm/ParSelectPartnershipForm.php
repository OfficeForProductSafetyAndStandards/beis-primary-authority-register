<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
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
  public function loadData(int $index = 1): void {
    $allow_multiple = $this->getConfiguration()['allow_multiple'] ?? FALSE;
    $this->getFlowDataHandler()->setFormPermValue("allow_multiple", (bool) $allow_multiple, $this);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $partnerships = $this->getFlowDataHandler()->getFormPermValue('partnerships');
    $partnership_options = $this->getParDataManager()->getEntitiesAsOptions($partnerships);
    $partnership_count = count($partnerships);

    // If no suggestions were found cancel out of the journey.
    if ($partnership_count <= 0) {
      $form['intro'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-form-group']],
        '#value' => $this->t('There are no partnerships to choose from.'),
      ];
    }
    // If only one partnership submit the form automatically and go to the next step.
    elseif ($partnership_count === 1) {
      $partnership_ids = [key($partnerships) => key($partnerships)];
      $this->getFlowDataHandler()->setTempDataValue('par_data_partnership_id', $partnership_ids);
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
          '#wrapper_attributes' => ['class' => ['govuk-!-margin-bottom-8', 'govuk-!-margin-top-8']],
        ];
      }

      // Initialize pager and get current page.
      $number_of_items = 10;
      $pager_id = implode('_', ['par_plugin', $this->getPluginId(), $index]);
      $pager = $this->getUniquePager()->getPager($pager_id);
      $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($partnerships), $number_of_items, $pager);

      // Split the items up into chunks:
      $chunks = array_chunk($partnership_options, $number_of_items, TRUE);
      $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];

      $default_value = $this->getDefaultValuesByKey("par_data_partnership_id", $index, NULL);
      $form['par_data_partnership_id'] = [
        '#type' => $multiple ? 'checkboxes' : 'radios',
        '#title' => $multiple ? $this->t('Choose a partnerships') : $this->t('Choose partnerships'),
        '#title_tag' => 'h2',
        '#options' => $partnership_options,
        '#default_value' => $multiple ? (array) $default_value : $default_value,
        '#attributes' => ['class' => ['govuk-form-group']],
      ];

      // If multiple selections are supported.
      if ($multiple) {
        $input_name = $this->getTargetName($this->getElementKey('select_all', $index));
        $form['par_data_partnership_id']['#states'] = [
          'visible' => [
            ':input[name="' . $input_name . '"]' => ['checked' => FALSE],
          ],
        ];
      }

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

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $partnerships = $this->getFlowDataHandler()
      ->getFormPermValue('partnerships');
    $multiple = $this->getFlowDataHandler()
      ->getDefaultValues('allow_multiple', TRUE);

    $partnership_id_key = $this->getElementKey('par_data_partnership_id', $index);

    // Populate partnership IDs if select all is checked.
    $select_all_key = $this->getElementKey('select_all', $index);
    if ($form_state->getValue($select_all_key) === self::SELECT_ALL) {
      $form_state->setValue($partnership_id_key, array_keys($partnerships));
    }

    // If multiple choices are allowed the resulting value may be an array with keys but empty values.
    $partnership_ids = $multiple ?
      NestedArray::filter((array) $form_state->getValue($partnership_id_key)) :
      $form_state->getValue($partnership_id_key);

    if (empty($partnership_ids)) {
      $id_key = $this->getElementKey('par_data_partnership_id', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($partnership_id_key), $this->wrapErrorMessage('You must select a partnership.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
