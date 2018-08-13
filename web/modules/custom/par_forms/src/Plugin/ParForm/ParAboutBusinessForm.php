<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "about_business",
 *   title = @Translation("About business form.")
 * )
 */
class ParAboutBusinessForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['about_business', 'par_data_organisation', 'comments', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter a description for the business.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    if ($par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $this->getFlowDataHandler()->setFormPermValue('about_business', $par_data_organisation->get('comments')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['about_business'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide information about the organisation'),
      '#default_value' => $this->getDefaultValuesByKey('about_business', $cardinality),
      '#description' => '<p>Use this section to give a brief overview of the organisation.</p><p>Include any information you feel may be useful to enforcing authorities.</p>',
    ];

    return $form;
  }
}
