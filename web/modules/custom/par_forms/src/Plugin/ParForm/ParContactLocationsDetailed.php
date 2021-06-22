<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Contact details display with locations.
 *
 * @ParForm(
 *   id = "contact_locations_detail",
 *   title = @Translation("Contact detail display with locations.")
 * )
 */
class ParContactLocationsDetailed extends ParFormPluginBase implements TrustedCallbackInterface {

  /**
   * Lists the trusted callbacks provided by this implementing class.
   *
   * Trusted callbacks are public methods on the implementing class and can be
   * invoked via
   * \Drupal\Core\Security\DoTrustedCallbackTrait::doTrustedCallback().
   *
   * @return string[]
   *   List of method names implemented by the class that can be used as trusted
   *   callbacks.
   *
   * @see \Drupal\Core\Security\DoTrustedCallbackTrait::doTrustedCallback()
   */
  public static function trustedCallbacks() {
    return [
      'getContactLocations',
    ];
  }

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  public function getPerson($cardinality = 1) {
    $contacts = $this->getFlowDataHandler()->getParameter('contacts');
    $contacts = !empty($contacts) ? array_values($contacts) : [];

    // Cardinality is not a zero-based index like the stored fields deltas.
    return isset($contacts[$cardinality-1]) ? $contacts[$cardinality-1] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $contact = $this->getPerson($cardinality);
    if ($contact instanceof ParDataEntityInterface) {
      $this->setDefaultValuesByKey("name", $cardinality, $contact->getFullName());
      $this->setDefaultValuesByKey("email", $cardinality, $contact->getEmail());
      $this->setDefaultValuesByKey("email_preferences", $cardinality, $contact->getEmailWithPreferences());
      $this->setDefaultValuesByKey("work_phone", $cardinality, $contact->getWorkPhone());
      $this->setDefaultValuesByKey("mobile_phone", $cardinality, $contact->getMobilePhone());

      $this->setDefaultValuesByKey("person_id", $cardinality, $contact->id());
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
      try {
        $params = ['par_data_person' => $this->getDefaultValuesByKey('person_id', $cardinality, NULL)];
        $title = 'Update ' . $this->getDefaultValuesByKey('name', $cardinality, 'person');
        $update_flow = ParFlow::load('person_update');
        $link = $update_flow ?
          $update_flow->getStartLink(1, $title, $params) : NULL;
        $actions = t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]);
      } catch (ParFlowException $e) {

      }

      $form['contact'] = [
        '#type' => 'fieldset',
        '#weight' => 1,
        '#attributes' => ['class' => ['grid-row', 'form-group', 'contact-details']],
        'name' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('name', $cardinality, NULL),
          '#attributes' => ['class' => ['column-two-thirds']],
        ],
        'actions' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => isset($actions) ? $actions : 'Update contact details',
          '#attributes' => ['class' => ['column-one-third']],
        ],
        'email' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->getDefaultValuesByKey('email_preferences', $cardinality, NULL),
          '#attributes' => ['class' => ['column-two-thirds']],
        ],
        'phone' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#attributes' => ['class' => ['column-one-third']],
          '#value' => $this->getDefaultValuesByKey('work_phone', $cardinality, NULL) . '<br>' . $this->getDefaultValuesByKey('mobile_phone', $cardinality, NULL),
        ],
        'locations' => [
          '#lazy_builder' => [
            static::class . '::getContactLocations',
            [$this->getDefaultValuesByKey('person_id', $cardinality, NULL), $cardinality]
          ],
          '#create_placeholder' => TRUE
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
   * Lazy loaded contact locations.
   *
   * This lookup can take too long to process for users with multiple contacts.
   */
  public static function getContactLocations($id, $cardinality) {
    $contact = $id ? ParDataPerson::load($id) : NULL;
    $locations = $contact instanceof ParDataEntityInterface ?
      $contact->getReferencedLocations() : NULL;

    $details = [
      '#type' => 'html_tag',
      '#tag' => 'details',
      '#attributes' => ['class' => ['column-full', 'contact-locations'], 'role' => 'group'],
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
        '#value' => !empty($locations) ? implode('<br>', $locations) : '',
      ],
    ];


    return $build = [
      '#type' => 'markup',
      '#markup' => \Drupal::service('renderer')->render($details),
    ];
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
    $contacts = $this->getFlowDataHandler()->getParameter('contacts');

    try {
      // Edit the legal entity.
      $params = ['par_data_person' => !empty($contacts) ? current($contacts)->id() : NULL];
      $options = ['attributes' => ['aria-label' => $this->t("Merge all the similar contact records")]];
      $merge_link = $this->getLinkByRoute('par_person_merge_flows.merge', $params, $options, TRUE);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }

    if (isset($merge_link) && $merge_link instanceof Link) {
      $actions['merge'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $merge_link->setText("merge contact records")->toString(),
        '#attributes' => ['class' => ['merge-people']],
      ];
    }

    return $actions;
  }
}
