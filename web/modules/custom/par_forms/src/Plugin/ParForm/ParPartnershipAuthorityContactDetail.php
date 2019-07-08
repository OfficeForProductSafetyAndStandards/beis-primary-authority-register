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
 *   id = "partnership_authority_contact_detail",
 *   title = @Translation("Partnership authority contact detail display.")
 * )
 */
class ParPartnershipAuthorityContactDetail extends ParFormPluginBase {

  protected $pagerId = 3235400188658;
  protected $numberPerPage = 5;

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
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership) {
      $contacts = $par_data_partnership->getAuthorityPeople();

      $current_page = $this->getFlowDataHandler()->getFormPermValue("page:{$this->pagerId}");
      if (!$current_page) {
        $current_page = pager_default_initialize(count($contacts), $this->numberPerPage, $this->pagerId);
        $this->getFlowDataHandler()->setFormPermValue("page:{$this->pagerId}", $current_page);
      }

      // Split the items up into chunks:
      $chunks = array_chunk($contacts, $this->numberPerPage);

      // Cardinality is not a zero-based index like the stored fields deltas.
      $contact = isset($chunks[$current_page][$cardinality - 1]) ? $chunks[$current_page][$cardinality - 1] : NULL;

      if ($contact instanceof ParDataEntityInterface) {
        $this->setDefaultValuesByKey("name", $cardinality, $contact->getFullName());
        $this->setDefaultValuesByKey("email", $cardinality, $contact->getEmail());
        $this->setDefaultValuesByKey("email_preferences", $cardinality, $contact->getEmailWithPreferences());
        $this->setDefaultValuesByKey("work_phone", $cardinality, $contact->getWorkPhone());
        $this->setDefaultValuesByKey("mobile_phone", $cardinality, $contact->getMobilePhone());

        $this->setDefaultValuesByKey("person_id", $cardinality, $contact->id());
      }
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
      $actions = [];
      // Get update contact link.
      try {
        $params = ['par_data_person' => $this->getDefaultValuesByKey('person_id', $cardinality, NULL)];
        $actions['update'] = t('@link', [
          '@link' => $this->getLinkByRoute('par_person_update_flows.update_contact', $params)
            ->setText('Update ' . $this->getDefaultValuesByKey('name', $cardinality, 'person'))
            ->toString(),
        ]);
      } catch (ParFlowException $e) {

      }

      // Get remove contact link.
      try {
        $params = ['par_data_person' => $this->getDefaultValuesByKey('person_id', $cardinality, NULL)];
        $actions['remove'] = t('@link', [
          '@link' => $this->getLinkByRoute('par_person_update_flows.update_contact', $params)
            ->setText('Update ' . $this->getDefaultValuesByKey('name', $cardinality, 'person'))
            ->toString(),
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
          '#value' => !empty($actions) ? implode(' &#9900; ') : '',
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
