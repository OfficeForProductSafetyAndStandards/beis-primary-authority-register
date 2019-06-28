<?php

namespace Drupal\par_partnership_flows\Controller;


use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\Controller\ParBaseInterface;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The Advice entity details page.
 */
class ParPartnershipFlowsAdvicePageController extends ParBaseController {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $par_data_partnership_advice = $this->getFlowDataHandler()->getParameter('par_data_advice');
    if ($par_data_partnership_advice) {
      $this->pageTitle = $par_data_partnership_advice->getAdviceTitle();
    }

    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {

    if ($par_data_advice->isArchived()) {
      $build['advice_details'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $build['advice_details']['archived'] = [
        '#type' => 'markup',
        '#markup' => 'Archived advice',
        '#prefix' => '<h2>',
        '#suffix' => '</h2>',
      ];
    }

    $build['notes'] = $this->renderSection('About this advice document', $par_data_advice, ['notes' => 'summary']);

    $build['advice_type'] = $this->renderSection('The type of advice', $par_data_advice, ['advice_type' => 'summary']);

    $build['regulatory_functions'] = $this->renderSection('Regulatory functions', $par_data_advice, ['field_regulatory_function' => 'full']);

    $build['issue_date'] = $this->renderSection('Issue date', $par_data_advice, ['issue_date' => 'full']);

    $build['advice_link'] = $this->renderSection('Advice documents', $par_data_advice, ['document' => 'title']);

    return parent::build($build);
  }
}
