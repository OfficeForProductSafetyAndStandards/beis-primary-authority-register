<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    if ($par_data_person instanceof ParDataEntityInterface) {
      $locations = $par_data_person->getReferencedLocations();
      $this->setDefaultValuesByKey("locations", $index, $locations);

      $this->setDefaultValuesByKey("person_id", $index, $par_data_person->id());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    if ($index === 1) {
      $form['message_intro'] = [
        '#type' => 'container',
        'heading' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Contact locations'),
          '#attributes' => ['class' => ['govuk-heading-l']],
        ],
        'info' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => 'This person may appear on other partnerships and in other places within the Primary Authority Register. Updating their information here will also update their details in all the places listed below.',
        ],
        '#attributes' => ['class' => ['govuk-form-group']],
      ];
    }

    if ($this->getDefaultValuesByKey('email', $index, NULL)) {
      $form['contact'] = [
        '#type' => 'container',
        '#weight' => 1,
        '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group', 'contact-details']],
        'locations' => [
          '#type' => 'html_tag',
          '#tag' => 'details',
          '#attributes' => ['class' => ['govuk-grid-column-full', 'govuk-details', 'contact-locations'], 'role' => 'group'],
          'summary' => [
            '#type' => 'html_tag',
            '#tag' => 'summary',
            '#attributes' => ['class' => ['govuk-details__summary'], 'role' => 'button', 'aria-controls' => "contact-detail-locations-$index"],
            '#value' => '<span class="govuk-details__summary-text">More information on where this contact is used</span>',
          ],
          'details' => [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => ['class' => ['govuk-details__text'], 'id' => "contact-detail-locations-$index"],
            'summary' => [
              '#theme' => 'item_list',
              '#items' => $this->getDefaultValuesByKey('locations', $index, []),
              '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
            ],
          ],
        ],
      ];
    }
    else {
      $form['contact'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group']],
        'heading' => [
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
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions(array $actions = [], array $data = NULL): ?array {
    return $actions;
  }
}
