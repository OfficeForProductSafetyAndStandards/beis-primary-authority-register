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
 * Contact details display with locations.
 *
 * @ParForm(
 *   id = "contact_locations",
 *   title = @Translation("Display contact locations.")
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
      $this->setDefaultValuesByKey("locations", $cardinality, implode('<br>', $locations));

      $this->setDefaultValuesByKey("person_id", $cardinality, $par_data_person->id());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    if ($cardinality === 1) {
      $form['message_intro'] = [
        '#type' => 'fieldset',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Contact locations'),
          '#attributes' => ['class' => ['heading-large']],
        ],
        'info' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => 'This person may appear on other partnerships and in other places within the Primary Authority Register. Updating their information here will also update their details in all the places listed below.',
        ],
        '#attributes' => ['class' => ['form-group']],
      ];
    }

    if ($this->getDefaultValuesByKey('email', $cardinality, NULL)) {
      $locations = [
        'summary' => [
          '#type' => 'html_tag',
          '#tag' => 'summary',
          '#attributes' => ['class' => ['form-group'], 'role' => 'button', 'aria-controls' => "contact-detail-locations-$cardinality"],
          '#value' => '<span class="summary">More information on where this contact is used</span>',
        ],
        'details' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => ['class' => ['form-group'], 'id' => "contact-detail-locations-$cardinality"],
          '#value' => $this->getDefaultValuesByKey('locations', $cardinality, ''),
        ],
      ];

      $form['contact'] = [
        '#type' => 'fieldset',
        '#weight' => 1,
        '#attributes' => ['class' => ['grid-row', 'form-group', 'contact-details']],
        'locations' => [
          '#type' => 'html_tag',
          '#tag' => 'details',
          '#attributes' => ['class' => ['column-full', 'contact-locations'], 'role' => 'group'],
          '#value' => \Drupal::service('renderer')->render($locations),
        ],
      ];
    }
    else {
      $form['contact'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('There are no contacts records listed.'),
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
