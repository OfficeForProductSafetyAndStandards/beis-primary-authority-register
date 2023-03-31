<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\Annotation\ParForm;
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

    $config = $this->getConfiguration();

    $form['panel'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-panel', 'govuk-panel--confirmation']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-panel__title']],
        '#value' => $this->t($config['panel_title']),
      ],
      'body' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => ['class' => ['govuk-panel__body']],
        '#value' => $this->t($config['panel_body']),
      ],
    ];

    if (!empty($config['info_paras'])) {
      $form['info_text'] = [
        '#type' => 'container',
      ];
      foreach ($config['info_paras'] as $i => $para) {
        $form['info_text'][$i] = [
          '#type' => 'markup',
          '#markup' => $this->t($para),
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
    }

    if (!empty($config['what_happens_next_paras'])) {
      $form['what_happens_next_paras'] = [
        '#type' => 'container',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('What happens next?'),
        ],
      ];
      foreach ($config['what_happens_next_paras'] as $i => $para) {
        $form['what_happens_next_paras'][$i] = [
          '#type' => 'markup',
          '#markup' => $this->t($para),
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
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
