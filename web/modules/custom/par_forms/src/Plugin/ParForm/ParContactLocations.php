<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Contact locations display plugin.
 *
 * This form only works on the basis of the url par_data_person context
 * and so can't have multiple iterations.
 *
 * @ParForm(
 *   id = "contact_locations",
 *   title = @Translation("Contact locations display.")
 * )
 */
class ParContactLocations extends ParFormPluginBase {

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    if ($par_data_person instanceof ParDataEntityInterface) {
      $locations = $par_data_person->getReferencedLocations();
      $this->setDefaultValuesByKey("locations", $cardinality, $locations);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    if ($locations = $this->getDefaultValuesByKey('locations', $cardinality, NULL)) {
      $form['locations'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group']],
        'summary' => [
          '#theme' => 'item_list',
          '#title' => $this->t('Please be aware that these details will be updated in all of the following places'),
          '#items' => $this->getDefaultValuesByKey('locations', $cardinality, []),
          '#attributes' => ['class' => ['list', 'form-group', 'list-bullet']],
        ],
      ];
    }

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
