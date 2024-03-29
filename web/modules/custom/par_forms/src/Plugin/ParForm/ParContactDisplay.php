<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Contact details display.
 *
 * @ParForm(
 *   id = "contact_display",
 *   title = @Translation("Contact detail display.")
 * )
 */
class ParContactDisplay extends ParFormPluginBase {

  public function getContacts() {
    // Get the configured field to get the contact records from.
    $contact_field = isset($this->getConfiguration()['contact_field']) ? (string) $this->getConfiguration()['contact_field'] : "field_person";

    // Decide which entity to use.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      switch ($contact_field) {
        case 'field_authority_person':
          $contacts = $par_data_partnership->getAuthorityPeople();
          break;

        case 'field_organisation_person':
          $contacts = $par_data_partnership->getOrganisationPeople();
          break;

      }
    }
    elseif ($par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority')) {
      $contacts = $par_data_authority->getPerson();
    }
    elseif ($par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $contacts = $par_data_organisation->getPerson();
    }

    return $contacts ?? NULL;
  }

  /**
   * Alter the number of items being displayed.
   */
  public function countItems($data = NULL): int {
    if ($contacts = $this->getContacts()) {
      return count($contacts);
    }
    else {
      return parent::countItems($data);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $contacts = $this->getContacts();

    $delta = $index - 1;

    // Reset the array keys so there are no gaps.
    $contacts = !empty($contacts) ? array_values($contacts) : [];
    // Cardinality is not a zero-based index like the stored fields deltas.
    $contact = $contacts[$delta] ?? NULL;

    if ($contact instanceof ParDataEntityInterface) {
      $this->setDefaultValuesByKey("name", $index, $contact->getFullName());
      $this->setDefaultValuesByKey("email", $index, $contact->getEmail());
      $this->setDefaultValuesByKey("email_preferences", $index, $contact->getEmailWithPreferences());
      $this->setDefaultValuesByKey("work_phone", $index, $contact->getWorkPhone());
      $this->setDefaultValuesByKey("mobile_phone", $index, $contact->getMobilePhone());

      $this->setDefaultValuesByKey("person_id", $index, $contact->id());
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
          '#value' => $this->t('Contacts'),
          '#attributes' => ['class' => ['z']],
        ],
        'info' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => 'It is possible for a person to have different contact details depending on the position they hold within an authority or organisation.',
        ],
        '#attributes' => ['class' => ['govuk-form-group']],
      ];
    }

    if ($this->getDefaultValuesByKey('email', $index, NULL)) {
      try {
        $params = ['par_data_person' => $this->getDefaultValuesByKey('person_id', $index, NULL)];
        $title = 'Update ' . $this->getDefaultValuesByKey('name', $index, 'person');
        $update_flow = ParFlow::load('person_update');
        $link = $update_flow ?
          $update_flow->getStartLink(1, $title, $params) : NULL;
        $actions = t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]);
      } catch (ParFlowException $e) {

      }

      $form['contact'] = [
        '#type' => 'container',
        '#weight' => 1,
        '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group', 'contact-details']],
        'name' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('name', $index, NULL),
          '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        ],
        'actions' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => isset($actions) ? $actions : 'Update contact details',
          '#attributes' => ['class' => ['govuk-grid-column-one-third']],
        ],
        'email' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('email_preferences', $index, NULL),
          '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        ],
        'phone' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#attributes' => ['class' => ['govuk-grid-column-one-third']],
          '#value' => $this->getDefaultValuesByKey('work_phone', $index, NULL) . '<br>' . $this->getDefaultValuesByKey('mobile_phone', $index, NULL),
        ],
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
