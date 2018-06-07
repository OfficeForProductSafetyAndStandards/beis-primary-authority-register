<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
  protected $formItems = [
    'par_data_partnership:partnership' => [
      'about_partnership' => 'about_partnership',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $this->getFlowDataHandler()->setFormPermValue('about_partnership', $par_data_partnership->get('about_partnership')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['about_partnership'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide information about the partnership'),
      '#default_value' => $this->getDefaultValuesByKey('about_partnership', $cardinality),
      '#description' => '<p>Use this section to give a brief overview of the partnership. Include any information you feel may be useful to enforcing authorities.</p>',
    ];

    return $form;
  }
}
