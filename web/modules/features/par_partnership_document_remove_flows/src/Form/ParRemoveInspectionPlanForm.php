<?php

namespace Drupal\par_partnership_document_remove_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_document_remove_flows\ParFlowAccessTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for removing inspection plans from a partnership.
 */
class ParRemoveInspectionPlanForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'Are you sure you want to remove this inspection plan?';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    // Set the inspection plan entity if a value was found for this delta.
    if ($par_data_partnership && $par_data_inspection_plan) {
      $inspection_plans = $par_data_partnership->get('field_inspection_plan')->getValue();
      // Note that this will only return the first instance of this $inspection_plans,
      // although this field should be unique so there shouldn't be more than one.
      $key = array_search($par_data_inspection_plan->id(), array_column($inspection_plans, 'target_id'));
      if ($key !== FALSE) {
        $this->getFlowDataHandler()->setFormPermValue('field_inspection_plan_delta', $key);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, $par_data_inspection_plan = NULL) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    $delta = $this->getFlowDataHandler()->getFormPermValue('field_inspection_plan_delta');

    // If there is no inspection plan skip this step.
    // @TODO Monitor PAR-1592. If a PR is submitted for that it will need
    // applying jto this method call too.
    if ($delta === NULL) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute('cancel'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    $document_view_builder = $this->getParDataManager()->getViewBuilder('file');
    $documents = $par_data_inspection_plan && $par_data_inspection_plan->hasField('document') && !$par_data_inspection_plan->get('document')->isEmpty()
      ? $document_view_builder->viewMultiple($par_data_inspection_plan->get('document')->referencedEntities(), 'title') : NULL;

    $form['remove'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Are you sure you want to remove the inspection plan @inspection_plan from the @partnership?', ['@inspection_plan' => $par_data_inspection_plan->getTitle(), '@partnership' => $par_data_partnership->label()]),
      '#attributes' => ['class' => ['remove-inspection-plan', 'form-group']],
    ];

    if (!empty($documents)) {
      $form['description'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('This will remove the following documents from this partnership.'),
        '#attributes' => ['class' => ['form-hint']],
      ];
      $form['documents'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->getRenderer()->render($documents),
        '#attributes' => ['class' => ['document-summary', 'form-group']],
      ];
    }

    $form['delta'] = [
      '#type' => 'hidden',
      '#value' => $delta,
    ];

    // Change the main button title to 'remove'.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Remove');

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    $delta = $this->getFlowDataHandler()->getTempDataValue('delta');

    // Remove the field delta.
    try {
      if (isset($delta)) {
        $par_data_partnership->get('field_inspection_plan')->removeItem($delta);
        $revision_message = $this->t("The inspection plan @inspection_plan was removed from the partnership.", ['@inspection_plan' => $par_data_inspection_plan->getTitle()]);
        $par_data_partnership->setNewRevision(TRUE, $revision_message);
      }
      else {
        throw new \InvalidArgumentException('No field delta has been provided.');
      }
    }
    catch (\InvalidArgumentException $e) {

    }

    // Don't save if there are no more inspection plan entities.
    if ($par_data_partnership->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The %inspection_plan could not be removed from the %field on partnership %partnership');
      $replacements = [
        '%inspection_plan' => $par_data_inspection_plan->label(),
        '%field' => $this->getFlowDataHandler()->getTempDataValue('field_inspection_plan'),
        '%partnership' => $par_data_partnership->label(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
