<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Date form plugin.
 *
 * @ParForm(
 *   id = "date",
 *   title = @Translation("Date.")
 * )
 */
class ParDateForm extends ParFormPluginBase {

  /**
   * @defaults
   */
  #[\Override]
  public function getFormDefaults(): array {
    return [
      'date' => ['year' => date('Y'), 'month' => date('m'), 'day' => date('d')],
    ];
  }

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    if ($coordinated_member = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business')) {
      $this->getFlowDataHandler()->setFormPermValue('date_membership_began', $coordinated_member->get('date_membership_began')->getString());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Membership begin date.
    $form['date'] = [
      '#type' => 'gds_date',
      '#title' => $this->t('Enter the date'),
      '#title_tag' => 'h2',
      '#description' => $this->t('For example: 01/01/2010'),
      '#default_value' => $this->getDefaultValuesByKey('date', $index, $this->getFormDefaultByKey('date')),
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $date_element = $this->getElement($form, ['date'], $index);
    $date = $date_element ? $form_state->getValue($date_element['#parents']) : NULL;
    $date_format = !empty($element['#date_date_format']) ? $element['#date_date_format'] : 'Y-m-d';

    try {
      DrupalDateTime::createFromFormat($date_format, $date, NULL, ['validate_format' => TRUE]);
    }
    catch (\Exception) {
      $message = 'The date format is not correct.';
      $this->setError($form, $form_state, $date_element, $message);
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
