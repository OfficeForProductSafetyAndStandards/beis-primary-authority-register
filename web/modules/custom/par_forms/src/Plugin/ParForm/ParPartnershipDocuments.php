<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Partnership documents display.
 *
 * @ParForm(
 *   id = "partnership_documents",
 *   title = @Translation("Partnership documents display.")
 * )
 */
class ParPartnershipDocuments extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $show_title = isset($this->getConfiguration()['show_title']) ? (bool) $this->getConfiguration()['show_title'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_title", $show_title);

    $show_inspection_plans = isset($this->getConfiguration()['inspection_plans']) ? (bool) $this->getConfiguration()['inspection_plans'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_inspection_plans", $show_inspection_plans);

    $show_advice_documents = isset($this->getConfiguration()['advice_documents']) ? (bool) $this->getConfiguration()['advice_documents'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_advice_documents", $show_advice_documents);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Partnership Documents - component.
    if ($this->getFlowDataHandler()->getFormPermValue("show_title")) {
      $form['documents'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => "Documents",
        '#attributes' => ['class' => 'heading-large'],
      ];
    }

    $form['details'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];

    // Inspection plan link.
    if ($this->getFlowDataHandler()->getFormPermValue("show_inspection_plans")) {
      $form['details']['inspection_plans'] = [
        '#type' => 'fieldset',
        '#title' => t('Inspection plans'),
        '#attributes' => ['class' => ['column-one-half']],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      // Add the inspection plan link safely with access checks.
      try {
        $add_inspection_list_link = $this->getFlowNegotiator()->getFlow()->getNextLink('inspection_plans', [], [], TRUE);
      } catch (ParFlowException $e) {

      }
      if (isset($add_inspection_list_link) && $add_inspection_list_link instanceof Link) {
        $form['details']['inspection_plans']['link'] = [
          '#type' => 'markup',
          '#markup' => $add_inspection_list_link->setText('See all Inspection Plans')
            ->toString(),
        ];
      }
    }

    // Add the advice link safely with access checks.
    if ($this->getFlowDataHandler()->getFormPermValue("show_advice_documents")) {
      $form['details']['advice'] = [
        '#type' => 'fieldset',
        '#title' => t('Advice and Documents'),
        '#attributes' => ['class' => ['column-one-half']],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      try {
        $add_advice_list_link = $this->getFlowNegotiator()
          ->getFlow()
          ->getNextLink('advice', [], [], TRUE);
      } catch (ParFlowException $e) {

      }
      if (isset($add_advice_list_link) && $add_advice_list_link instanceof Link) {
        $form['details']['advice']['link'] = [
          '#type' => 'markup',
          '#markup' => $add_advice_list_link->setText('See all Advice')
            ->toString(),
        ];
      }
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($cardinality = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}
