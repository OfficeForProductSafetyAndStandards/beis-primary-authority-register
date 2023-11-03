<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About partnership form plugin.
 *
 * @ParForm(
 *   id = "about_partnership",
 *   title = @Translation("About partnership form.")
 * )
 */
class ParAboutPartnershipForm extends ParFormPluginBase {


  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['about_partnership', 'par_data_partnership', 'about_partnership', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter some information about this partnership.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $this->getFlowDataHandler()->setFormPermValue('about_partnership', $par_data_partnership->getPlain('about_partnership'));
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {

    $form['partnership'] = [
      '#type' => 'container'
    ];
    $title_id = Html::cleanCssIdentifier("{$this->getPluginId()}-$index-about-title");
    $form['partnership']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Provide information about the partnership'),
      '#attributes' => [
        'id' => $title_id,
        'class' => ['govuk-heading-m']
      ],
    ];
    $form['partnership']['about_partnership'] = [
      '#type' => 'textarea',
      '#default_value' => $this->getDefaultValuesByKey('about_partnership', $index),
      '#description' => 'Use this section to give a brief overview of the partnership. Include any information you feel may be useful to enforcing authorities.',
      '#attributes' => [
        'aria-labelledby' => $title_id,
      ],
    ];

    return $form;
  }
}
