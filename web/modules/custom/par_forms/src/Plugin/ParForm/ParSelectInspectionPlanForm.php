<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A form for submission of general enquiries.
 *
 * @ParForm(
 *   id = "select_inspection_plan",
 *   title = @Translation("Select inspection plan form.")
 * )
 */
class ParSelectInspectionPlanForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      $inspection_plans = $par_data_partnership->getInspectionPlan();
      $options = $inspection_plans ?
        $this->getParDataManager()->getEntitiesAsOptions((array) $inspection_plans) :
        [];
      $this->getFlowDataHandler()->setFormPermValue('inspection_plan_options', $options);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $inspection_plans = $this->getFlowDataHandler()->getFormPermValue('inspection_plan_options');

    if (!$inspection_plans or count($inspection_plans) <= 0) {
      $form['no_inspection_plans'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('There are no inspection plans'),
        'text' => [
          '#type' => 'markup',
          '#markup' => $this->t("This partnership is not covered by any inspection plans, please contact the primary authority to request an inspection plan be added first."),
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ],
      ];

      return $form;
    }
    elseif (count($inspection_plans) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('inspection_plan_id', key($inspection_plans));
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    // Checkboxes for inspection plans.
    $form['inspection_plan_id'] = [
      '#type' => 'checkboxes',
      '#attributes' => ['class' => ['form-group']],
      '#title' => t('Choose which inspection plan you\'re request is related to'),
      '#options' => $inspection_plans,
      // Automatically check all legal entities if no form data is found.
      '#default_value' => $this->getDefaultValuesByKey('inspection_plan', $index, []),
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $inspection_plan_key = $this->getElementKey('inspection_plan_id');
    $inspection_plan_values = array_filter($form_state->getValue($inspection_plan_key) ?? []);
    if (!$inspection_plan_values) {
      $inspection_plans = $this->getFlowDataHandler()->getFormPermValue('inspection_plan_options');
      $message = (!$inspection_plans or count($inspection_plans) <= 0) ?
        "You can not complete this journey because this partnership doesn't have any inspection plans." :
        "You must select at least one inspection plan.";

      $id_key = $this->getElementKey('inspection_plan_id', $index, TRUE);
      $message = $this->wrapErrorMessage($message, $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($inspection_plan_key), $message);
    }

    return parent::validate($form, $form_state, $index, $action);
  }
}
