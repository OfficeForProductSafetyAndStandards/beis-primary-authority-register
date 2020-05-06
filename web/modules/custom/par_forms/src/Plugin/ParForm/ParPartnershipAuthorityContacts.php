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
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($authority_contacts = $par_data_partnership->getAuthorityPeople()) {
      $this->setDefaultValuesByKey("authority_people", $cardinality, $authority_contacts);
    }
    if ($organisation_contacts = $par_data_partnership->getOrganisationPeople()) {
      $this->setDefaultValuesByKey("organisation_people", $cardinality, $organisation_contacts);
    }

    // Set configuration options.
    $show_title = isset($this->getConfiguration()['show_title']) ? (bool) $this->getConfiguration()['show_title'] : TRUE;
    $this->getFlowDataHandler()->setFormPermValue("show_title", $show_title);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    if ($this->getFlowDataHandler()->getFormPermValue("show_title")) {
      $form['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => "Contact information",
        '#attributes' => ['class' => 'heading-large'],
      ];
    }

    // AUTHORITY CONTACTS.
    $authority_contacts = $this->getDefaultValuesByKey('authority_people', $cardinality, []);

    // Initialize pager and get current page.
    $number_per_page = 5;
    $pager = $this->getUniquePager()->getPager('partnership_manage_authority_contacts');
    $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($authority_contacts), $number_per_page, $pager);

    // Get update and remove links.
    try {
      $params = $this->getRouteParams() + ['type' => 'authority'];
      if ($link = $this->getLinkByRoute('par_partnership_contact_add_flows.create_contact', $params, [], TRUE)) {
        $add_authority_contact_link = $link->setText('add another authority contact')->toString();
      }
    } catch (ParFlowException $e) {

    }

    $form['authority_contacts'] = [
      '#type' => 'fieldset',
      '#title' => t('Primary Authority'),
      '#attributes' => ['class' => ['form-group']],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'field_authority_person' => [
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
            '#value' => !empty($add_authority_contact_link) ? $add_authority_contact_link : '',
          ],
        ],
      ],
    ];

    // Split the items up into chunks:
    $chunks = array_chunk($authority_contacts, $number_per_page);
    foreach ($chunks[$current_pager->getCurrentPage()] as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $entity_view = $entity_view_builder->view($entity, 'detailed');
      $rendered_field = $this->getRenderer()->render($entity_view);

      // Get update and remove links.
      try {
        $params = $this->getRouteParams() + ['type' => 'authority', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_update_flows.create_contact', $params, [], TRUE)) {
          $update_authority_contact_link = t('@link', [
            '@link' => $link->setText('edit ' . strtolower($entity->label()))->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }
      try {
        $params = $this->getRouteParams() + ['type' => 'authority', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_remove_flows.remove', $params, [], TRUE)) {
          $remove_authority_contact_link = t('@link', [
            '@link' => $link->setText('remove ' . strtolower($entity->label()) . ' from this partnership')->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }

      $form['authority_contacts']['field_authority_person']['items'][$delta] = [
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
            '#value' => !empty($update_authority_contact_link) ? $update_authority_contact_link : '',
            '#attributes' => ['class' => ['column-one-third']],
          ],
          'remove' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($remove_authority_contact_link) ? $remove_authority_contact_link : '',
            '#attributes' => ['class' => ['column-two-thirds']],
          ],
        ],
      ];
    }

    // Display all the organisational contacts.
    $organisation_contacts = $this->getDefaultValuesByKey('organisation_people', $cardinality, []);

    // Initialize pager and get current page.
    $number_per_page = 5;
    $pager = $this->getUniquePager()->getPager('partnership_manage_organisation_contacts');
    $current_pager = $this->getUniquePager()->getPagerManager()->createPager(count($organisation_contacts), $number_per_page, $pager);

    // Get update and remove links.
    try {
      $params = $this->getRouteParams() + ['type' => 'organisation'];
      if ($link = $this->getLinkByRoute('par_partnership_contact_add_flows.create_contact', $params, [], TRUE)) {
        $add_organisation_contact_link = t('@link', [
          '@link' => $link->setText('add another organisation contact')->toString(),
        ]);
      }
    } catch (ParFlowException $e) {

    }

    $form['organisation_contacts'] = [
      '#type' => 'fieldset',
      '#title' => t('Organisation'),
      '#attributes' => ['class' => ['form-group']],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'field_organisation_person' => [
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
            '#value' => !empty($add_organisation_contact_link) ? $add_organisation_contact_link : '',
          ],
        ],
      ],
    ];

    // Split the items up into chunks:
    $chunks = array_chunk($organisation_contacts, $number_per_page);
    foreach ($chunks[$current_pager->getCurrentPage()] as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $entity_view = $entity_view_builder->view($entity, 'detailed');
      $rendered_field = $this->getRenderer()->render($entity_view);

      // Get update and remove links.
      try {
        $params = $this->getRouteParams() + ['type' => 'organisation', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_update_flows.create_contact', $params, [], TRUE)) {
          $update_organisation_contact_link = t('@link', [
            '@link' => $link->setText('edit ' . strtolower($entity->label()))->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }
      try {
        $params = $this->getRouteParams() + ['type' => 'organisation', 'par_data_person' => $entity->id()];
        if ($link = $this->getLinkByRoute('par_partnership_contact_remove_flows.remove', $params, [], TRUE)) {
          $remove_organisation_contact_link = t('@link', [
            '@link' => $link->setText('remove ' . strtolower($entity->label()) . ' from this partnership')->toString(),
          ]);
        }
      } catch (ParFlowException $e) {

      }

      $form['organisation_contacts']['field_organisation_person']['items'][$delta] = [
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
            '#value' => !empty($update_organisation_contact_link) ? $update_organisation_contact_link : '',
            '#attributes' => ['class' => ['column-one-third']],
          ],
          'remove' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => !empty($remove_organisation_contact_link) ? $remove_organisation_contact_link : '',
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
