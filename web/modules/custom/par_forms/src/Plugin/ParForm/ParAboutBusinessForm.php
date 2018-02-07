<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
  protected $formItems = [
    'par_data_organisation:organisation' => [
      'comments' => 'about_business',
    ],
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
      '#title' => $this->t('Provide information about the business'),
      '#default_value' => $this->getDefaultValuesByKey('about_business', $cardinality),
      '#description' => '<p>Use this section to give a brief overview of the business.</p><p>Include any information you feel may be useful to enforcing authorities.</p>',
    ];

    return $form;
  }
}
