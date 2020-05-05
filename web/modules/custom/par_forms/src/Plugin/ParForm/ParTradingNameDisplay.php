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
 * Organisation information display for partnerships.
 *
 * @ParForm(
 *   id = "organisation_information_display",
 *   title = @Translation("Organisation information display.")
 * )
 */
class ParOrganisationInformationDisplay extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface
      && $par_data_organisation = $par_data_partnership->getOrganisation(TRUE)) {
      // Format the address.
      if ($par_data_organisation->hasField('field_premises')
        && $address = $par_data_organisation->getPremises(TRUE)) {
        $this->setDefaultValuesByKey("address", $cardinality, $address);
      }

      // Get the partnership information.
      if ($par_data_organisation->hasField('comments')) {
        $information = $par_data_organisation->comments->view('about');
        $this->setDefaultValuesByKey("information", $cardinality, $information);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Partnership Organisation Information - component.
    $form['organisation_info'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Information about the organisation",
      '#attributes' => ['class' => 'heading-large'],
    ];

    // Display the address.
    $address = $this->getDefaultValuesByKey('address', $cardinality, NULL);
    $entity_view_builder = $address ? $this->getParDataManager()->getViewBuilder($address->getEntityTypeId()) : NULL;
    $address_entity = $entity_view_builder->view($address, 'summary');
    $form['registered_address'] = [
      '#type' => 'fieldset',
      '#title' => 'Address',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'field_premises' => [
        '#type' => 'container',
        'address' => $address_entity,
      ],
    ];

    // Add a link to edit the address.
    try {
      $params[$address->getEntityTypeId()] = $address->id();
      $address_edit_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_field_premises', $params, [], TRUE);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($address_edit_link)) {
      $form['registered_address']['field_premises']['edit'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $address_edit_link->setText("edit address")->toString(),
        '#attributes' => ['class' => 'edit-address'],
      ];
    }

    // Display details about the partnership for information.
    $form['about'] = [
      '#type' => 'fieldset',
      '#title' => 'About the organisation',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'details' => $this->getDefaultValuesByKey('information', $cardinality, NULL),
    ];
    try {
      $about_edit_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_comments', [], [], TRUE);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($about_edit_link)) {
      $form['about']['edit'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $about_edit_link->setText("edit about the partnership")->toString(),
        '#attributes' => ['class' => 'edit-about-organisation'],
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
