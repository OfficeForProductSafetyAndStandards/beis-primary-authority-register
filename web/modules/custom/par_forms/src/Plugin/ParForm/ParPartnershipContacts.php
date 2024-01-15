<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Partnership contact display.
 *
 * @ParForm(
 *   id = "partnership_contacts",
 *   title = @Translation("Partnership contact display.")
 * )
 */
class ParPartnershipContacts extends ParFormPluginBase {

  /**
   * Available contact display options.
   */
  const AUTHORITY_CONTACTS = 'authority';
  const ORGANISATION_CONTACTS = 'organisation';

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Set display configuration options.
    $available_formats = [self::AUTHORITY_CONTACTS, self::AUTHORITY_CONTACTS];
    $format = isset($this->getConfiguration()['display']) && array_search($this->getConfiguration()['display'], $available_formats) !== FALSE
      ? $this->getConfiguration()['display'] : self::ORGANISATION_CONTACTS;
    $this->getFlowDataHandler()->setFormPermValue("contact_format", $format, $this);

    if ($format === self::AUTHORITY_CONTACTS && $authority_contacts = $par_data_partnership->getAuthorityPeople()) {
      $this->setDefaultValuesByKey("authority_people", $index, $authority_contacts);
    }
    elseif ($format === self::ORGANISATION_CONTACTS && $organisation_contacts = $par_data_partnership->getOrganisationPeople()) {
      $this->setDefaultValuesByKey("organisation_people", $index, $organisation_contacts);
    }

    // Set title display options.
    $show_title = isset($this->getConfiguration()['show_title']) ? (bool) $this->getConfiguration()['show_title'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_title", $show_title, $this);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    if ($this->getFlowDataHandler()->getFormPermValue("show_title", $this)) {
      $form['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => "Contact information",
        '#attributes' => ['class' => 'govuk-heading-l'],
      ];
    }

    $contact_format = $this->getFlowDataHandler()->getFormPermValue("contact_format", $this);
    switch ($contact_format) {
      case self::AUTHORITY_CONTACTS:
        $contacts = $this->getDefaultValuesByKey('authority_people', $index, []);
        $section_title = 'Primary Authority';
        break;

      case self::ORGANISATION_CONTACTS:
        $contacts = $this->getDefaultValuesByKey('organisation_people', $index, []);
        $section_title = 'Organisation';
        break;

      default:
        // Do not display the form without a pre-set default.
        return $form;
    }

    // Initialize pager and get current page.
    $number_per_page = 5;
    $pager = $this->getUniquePager()->getPager("partnership_manage_{$contact_format}_contacts");
    $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($contacts), $number_per_page, $pager);

    // Get update and remove links.
    try {
      $params = ['type' => $contact_format];
      $contact_add_flow = ParFlow::load('partnership_contact_add');
      $add_contact_link = $contact_add_flow?->getStartLink(1, "add another {$contact_format} contact", $params);
    } catch (ParFlowException $e) {

    }

    $form["{$contact_format}_contacts"] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $section_title,
      ],
      '#attributes' => ['class' => ['govuk-form-group']],
      'person' => [
        '#type' => 'container',
        'items' => [
          '#type' => 'container'
        ],
        'pager' => [
          '#type' => 'pager',
          '#theme' => 'pagerer',
          '#element' => $pager,
          '#weight' => 100,
          '#config' => [
            'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
          ],
        ],
        'operations' => [
          '#type' => 'container',
          'add' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $add_contact_link ? $add_contact_link->toString() : '',
          ],
        ],
      ],
    ];

    // Split the items up into chunks:
    $chunks = array_chunk($contacts, $number_per_page);
    $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];
    foreach ($chunk as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $entity_view = $entity_view_builder->view($entity, 'detailed');

      // Get the remove link.
      try {
        $params = ['type' => $contact_format, 'par_data_person' => $entity->id()];
        $contact_remove_flow = ParFlow::load('partnership_contact_remove');
        $remove_contact_link = $contact_remove_flow?->getStartLink(1, 'remove ' . strtolower($entity->label()) . ' from this partnership', $params);
      } catch (ParFlowException $e) {

      }

      $form["{$contact_format}_contacts"]['person']['items'][$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group', 'contact-details']],
        'entity' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => ['class' => ['govuk-grid-column-full']],
          [...$entity_view],
        ],
        'operations' => [
          'remove' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $remove_contact_link ? $remove_contact_link?->toString() : '',
            '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
          ],
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
