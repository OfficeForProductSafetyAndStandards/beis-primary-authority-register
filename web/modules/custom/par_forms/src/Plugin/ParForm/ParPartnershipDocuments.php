<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $advice_document_count = $par_data_partnership->countReferencedEntity('field_advice', FALSE);
    $this->getFlowDataHandler()->setFormPermValue("advice_count", $advice_document_count);

    $inspection_plan_count = $par_data_partnership->countReferencedEntity('field_inspection_plan', FALSE);
    $this->getFlowDataHandler()->setFormPermValue("isp_count", $inspection_plan_count);

    $show_title = isset($this->getConfiguration()['show_title']) ? (bool) $this->getConfiguration()['show_title'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_title", $show_title);

    $show_inspection_plans = isset($this->getConfiguration()['inspection_plans']) ? (bool) $this->getConfiguration()['inspection_plans'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_inspection_plans", $show_inspection_plans);

    $show_advice_documents = isset($this->getConfiguration()['advice_documents']) ? (bool) $this->getConfiguration()['advice_documents'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_advice_documents", $show_advice_documents);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Partnership Documents - component.
    if ($this->getFlowDataHandler()->getFormPermValue("show_title")) {
      $form['documents'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => "Documents",
        '#attributes' => ['class' => 'govuk-heading-l'],
      ];
    }

    $form['details'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group']],
    ];

    // Inspection plan link.
    if ($this->getFlowDataHandler()->getFormPermValue("show_inspection_plans")) {
      $form['details']['inspection_plans'] = [
        '#type' => 'container',
        'heading' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#attributes' => ['class' => ['govuk-heading-m']],
          '#value' => $this->t('Inspection plans'),
        ],
        '#attributes' => ['class' => ['govuk-grid-column-one-half']]
      ];

      // Add the inspection plan link safely with access checks.
      try {
        $add_inspection_list_link = $this->getFlowNegotiator()->getFlow()->getFlowLink('inspection_plans', 'See all Inspection Plans');
      } catch (ParFlowException) {

      }
      if (isset($add_inspection_list_link) && $add_inspection_list_link instanceof Link) {
        $isp_count = $this->getDefaultValuesByKey('isp_count', $index, '0');

        $form['details']['inspection_plans']['document_count'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->formatPlural($isp_count,
              'There is <strong>@count</strong> inspection plan covered by this partnership.',
              'There are <strong>@count</strong> inspection plans covered by this partnership.',
              ['@count' => $isp_count]
            ),
        ];

        $form['details']['inspection_plans']['link'] = [
          '#type' => 'markup',
          '#markup' => $add_inspection_list_link->toString(),
        ];
      }
    }

    // Add the advice link safely with access checks.
    if ($this->getFlowDataHandler()->getFormPermValue("show_advice_documents")) {
      $form['details']['advice'] = [
        '#type' => 'container',
        'heading' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#attributes' => ['class' => ['govuk-heading-m']],
          '#value' => $this->t('Advice and Documents'),
        ],
        '#attributes' => ['class' => ['govuk-grid-column-one-half']]
      ];

      try {
        $add_advice_list_link = $this->getFlowNegotiator()->getFlow()->getFlowLink('advice', 'See all Advice');
      } catch (ParFlowException) {

      }
      if (isset($add_advice_list_link) && $add_advice_list_link instanceof Link) {
        $count = $this->getDefaultValuesByKey('advice_count', $index, '0');

        $form['details']['advice']['document_count'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->formatPlural($count,
              'There is <strong>@count</strong> advice document covered by this partnership.',
              'There are <strong>@count</strong> advice documents covered by this partnership.',
              ['@count' => $count]
            ),
        ];

        $form['details']['advice']['link'] = [
          '#type' => 'markup',
          '#markup' => $add_advice_list_link->toString(),
        ];
      }
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getComponentActions(array $actions = [], array $data = NULL): ?array {
    return $actions;
  }
}
