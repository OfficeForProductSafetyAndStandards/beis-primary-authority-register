<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "member_name",
 *   title = @Translation("Member organisation name form.")
 * )
 */
class ParMemberNameForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['name', 'par_data_organisation', 'organisation_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => "You must enter the member's name.",
      'This value should not be null.' => "You must enter the member's name.",
    ]],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData(int $index = 1): void {
    if ($par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $this->getFlowDataHandler()->setFormPermValue('name', $par_data_organisation->get('organisation_name')->getString());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the member organisation name'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('name'),
    ];

    return $form;
  }
}
