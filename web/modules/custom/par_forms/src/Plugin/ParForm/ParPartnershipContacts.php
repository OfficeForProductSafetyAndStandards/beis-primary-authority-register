<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
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
  public function loadData($cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Set display configuration options.
    $available_formats = [self::AUTHORITY_CONTACTS, self::AUTHORITY_CONTACTS];
    $format = isset($this->getConfiguration()['display']) && array_search($this->getConfiguration()['display'], $available_formats) !== FALSE
      ? $this->getConfiguration()['display'] : self::ORGANISATION_CONTACTS;
    $this->getFlowDataHandler()->setFormPermValue("contact_format", $format, $this);

    if ($format === self::AUTHORITY_CONTACTS && $authority_contacts = $par_data_partnership->getAuthorityPeople()) {
      $this->setDefaultValuesByKey("authority_people", $cardinality, $authority_contacts);
    }
    elseif ($format === self::ORGANISATION_CONTACTS && $organisation_contacts = $par_data_partnership->getOrganisationPeople()) {
      $this->setDefaultValuesByKey("organisation_people", $cardinality, $organisation_contacts);
    }

    // Set title display options.
    $show_title = isset($this->getConfiguration()['show_title']) ? (bool) $this->getConfiguration()['show_title'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_title", $show_title, $this);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    if ($this->getFlowDataHandler()->getFormPermValue("show_title", $this)) {
      $form['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => "Contact information",
        '#attributes' => ['class' => 'heading-large'],
      ];
    }

    $contact_format = $this->getFlowDataHandler()->getFormPermValue("contact_format", $this);
    switch ($contact_format) {
      case self::AUTHORITY_CONTACTS:
        $contacts = $this->getDefaultValuesByKey('authority_people', $cardinality, []);
        $section_title = 'Primary Authority';
        break;

      case self::ORGANISATION_CONTACTS:
        $contacts = $this->getDefaultValuesByKey('organisation_people', $cardinality, []);
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
      if ($link = $this->getLinkByRoute('par_partnership_contact_add_flows.create_contact', $params, [], TRUE)) {
        $add_contact_link = $link->setText("add another {$contact_format} contact")->toString();
      }
    } catch (ParFlowException $e) {

    }

    $form["{$contact_format}_contacts"] = [
      '#type' => 'fieldset',
      '#title' => $section_title,
      '#attributes' => ['class' => ['form-group']],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'person' => [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'items' => [
          '#type' => 'fieldset',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
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
          '#type' => 'fieldset',
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
          'add' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($add_contact_link) ? $add_contact_link : '',
          ],
        ],
      ],
    ];

    // Split the items up into chunks:
    $chunks = array_chunk($contacts, $number_per_page);
    foreach ($chunks[$current_pager->getCurrentPage()] as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $entity_view = $entity_view_builder->view($entity, 'detailed');
      $rendered_field = $this->getRenderer()->render($entity_view);

      // Get update and remove links.
      try {
        $params = ['type' => $contact_format, 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_update_flows.create_contact', $params, [], TRUE)) {
          $update_contact_link = $link->setText('edit ' . strtolower($entity->label()))->toString();
        }
      } catch (ParFlowException $e) {

      }
      try {
        $params = ['type' => $contact_format, 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_remove_flows.remove', $params, [], TRUE)) {
          $remove_contact_link = $link->setText('remove ' . strtolower($entity->label()) . ' from this partnership')->toString();
        }
      } catch (ParFlowException $e) {

      }

      $form["{$contact_format}_contacts"]['person']['items'][$delta] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['grid-row', 'form-group', 'contact-details']],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'entity' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $rendered_field ? $rendered_field : '<p>(none)</p>',
          '#attributes' => ['class' => ['column-full']],
        ],
        'operations' => [
          'update' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($update_contact_link) ? $update_contact_link : '',
            '#attributes' => ['class' => ['column-one-third']],
          ],
          'remove' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($remove_contact_link) ? $remove_contact_link : '',
            '#attributes' => ['class' => ['column-two-thirds']],
          ],
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
