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
 * Contact details display plugin.
 *
 * @ParForm(
 *   id = "contact_detail",
 *   title = @Translation("Contact detail display.")
 * )
 */
class ParContactDetail extends ParFormPluginBase {

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
    $contacts = $this->getFlowDataHandler()->getParameter('contacts');
    $contacts = !empty($contacts) ? array_values($contacts) : [];
    // Cardinality is not a zero-based index like the stored fields deltas.
    $contact = isset($contacts[$cardinality-1]) ? $contacts[$cardinality-1] : NULL;

    if ($contact instanceof ParDataEntityInterface) {
      $this->setDefaultValuesByKey("name", $cardinality, $contact->getFullName());
      $this->setDefaultValuesByKey("email", $cardinality, $contact->getEmail());
      $this->setDefaultValuesByKey("email_preferences", $cardinality, $contact->getEmailWithPreferences());
      $this->setDefaultValuesByKey("work_phone", $cardinality, $contact->getWorkPhone());
      $this->setDefaultValuesByKey("mobile_phone", $cardinality, $contact->getMobilePhone());

      $locations = $contact->getReferencedLocations();
      $this->setDefaultValuesByKey("locations", $cardinality, implode('<br>', $locations));

      $this->getFlowDataHandler()->setFormPermValue("person_id", $contact->id());
    }
    if ($user = $this->getFlowDataHandler()->getParameter('user')) {
      $this->getFlowDataHandler()->setFormPermValue("user_id", $user->id());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    if ($cardinality === 1) {
      $form['message_intro'] = [
        '#type' => 'fieldset',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Contacts'),
          '#attributes' => ['class' => ['heading-large']],
        ],
        'info' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => 'It is possible for a person to have different contact details depending on the position they hold within an authority or organisation.',
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
        'name' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('name', $cardinality, NULL),
          '#attributes' => ['class' => ['column-one-third']],
        ],
        'email' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('email_preferences', $cardinality, NULL),
          '#attributes' => ['class' => ['column-one-third']],
        ],
        'phone' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#attributes' => ['class' => ['column-one-third']],
          '#value' => $this->getDefaultValuesByKey('work_phone', $cardinality, NULL) . '<br>' . $this->getDefaultValuesByKey('mobile_phone', $cardinality, NULL),
        ],
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
