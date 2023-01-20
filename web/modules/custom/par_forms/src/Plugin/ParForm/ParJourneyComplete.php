<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Journey complete form showing green panel and informative text.
 *
 * @ParForm(
 *   id = "journey_complete",
 *   title = @Translation("Journey complete panel and informative text.")
 * )
 */
class ParJourneyComplete extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $panel_title = 'Partnership amendment complete';
    $panel_body = 'Now awaiting approval by the business organisation';

    // Conditions text.
    $form['panel'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-panel', 'govuk-panel--confirmation']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-panel__title']],
        '#value' => $this->t($panel_title),
      ],
      'body' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => ['class' => ['govuk-panel__body']],
        '#value' => $this->t($panel_body),
      ],
    ];

    if (!empty($info_text)) {
      $form['info_text'] = [
        '#type' => 'markup',
        '#markup' => $this->t($info_text),
      ];

    }

    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Done');

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
