<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "authority_name",
 *   title = @Translation("Authority name form.")
 * )
 */
class ParAuthorityNameForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['name', 'par_data_authority', 'authority_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => "You must enter the authority's name.",
      'This value should not be null.' => "You must enter the authority's name.",
    ]],
  ];


  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    if ($par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority')) {
      $this->getFlowDataHandler()->setFormPermValue('name', $par_data_authority->get('authority_name')->getString());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the authority name'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('name'),
    ];

    return $form;
  }
}
