<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Select an authority type
 *
 * @ParForm(
 *   id = "authority_type",
 *   title = @Translation("Select an authority type form.")
 * )
 */
class ParAuthorityTypeForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [];

  /**
   * Load the data for this form.
   */
  public function loadData(int $index = 1): void {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');
    if ($par_data_authority) {
        $this->getFlowDataHandler()->setFormPermValue("authority_type", $par_data_authority->get('authority_type')->getString());
    }

    $authority_bundle = $this->getParDataManager()->getParBundleEntity('par_data_authority');
    $types = $authority_bundle->getAllowedValues('authority_type');

    if ($types) {
      $this->getFlowDataHandler()
        ->setFormPermValue("authority_type_options", (array) $types);
    }


    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $form['authority_type'] = [
      '#type' => 'radios',
      '#title' => 'Choose an authority type',
      '#attributes' => ['class' => ['form-group']],
      '#options' => $this->getFlowDataHandler()->getFormPermValue('authority_type_options'),
      '#default_value' => $this->getDefaultValuesByKey('authority_type', $index, NULL),
    ];

    return $form;
  }
}
