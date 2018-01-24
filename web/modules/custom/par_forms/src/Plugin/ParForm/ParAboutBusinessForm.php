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
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_organisation:organisation' => [
      'comments' => 'about_business',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $partnership ? $partnership->getOrganisation(TRUE) : NULL;

    $this->getFlowDataHandler()->setFormPermValue('about_business', $par_data_organisation->get('comments')->getString());

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = []) {
    $form['about_business'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide information about the business'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('about_business'),
      '#description' => '<p>Use this section to give a brief overview of the business.</p><p>Include any information you feel may be useful to enforcing authorities.</p>',
    ];

    return $form;
  }
}
