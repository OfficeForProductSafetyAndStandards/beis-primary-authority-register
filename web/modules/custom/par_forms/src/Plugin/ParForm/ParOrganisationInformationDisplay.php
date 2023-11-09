<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
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
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface
      && $par_data_organisation = $par_data_partnership->getOrganisation(TRUE)) {
      // Format the address.
      if ($par_data_organisation->hasField('field_premises')
        && $address = $par_data_organisation->getPremises(TRUE)) {
        $this->setDefaultValuesByKey("address", $index, $address);
      }

      // Get the partnership information.
      if ($par_data_organisation->hasField('comments')) {
        $information = $par_data_organisation->comments->view('about');
        $this->setDefaultValuesByKey("information", $index, $information);
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Partnership Organisation Information - component.
    $form['organisation_info'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "Information about the organisation",
      '#attributes' => ['class' => 'govuk-heading-l'],
    ];

    // Display the address.
    $address = $this->getDefaultValuesByKey('address', $index, NULL);
    $entity_view_builder = $address instanceof EntityInterface ?
      $this->getParDataManager()->getViewBuilder($address->getEntityTypeId()) : NULL;
    $rendered_address = $entity_view_builder instanceof EntityViewBuilderInterface ?
      $entity_view_builder->view($address, 'summary') : NULL;
    $form['registered_address'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Address'),
      ],
      '#attributes' => ['class' => 'govuk-form-group']
    ];
    if ($rendered_address) {
      $form['registered_address']['field_premises'] = [
        '#type' => 'container',
        'address' => $rendered_address,
      ];
    }

    // Add a link to edit the address.
    try {
      if ($address instanceof EntityInterface) {
        $params[$address->getEntityTypeId()] = $address->id();
        $address_edit_link = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('edit_field_premises', 'edit address', $params);
      }
      else {
        $address_add_link = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('add_field_premises', 'add address');
      }
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($address_edit_link)) {
      $form['registered_address']['field_premises']['edit'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $address_edit_link->toString(),
        '#attributes' => ['class' => 'edit-address'],
      ];
    }
    elseif (isset($address_add_link)) {
      $form['registered_address']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $address_add_link->toString(),
        '#attributes' => ['class' => 'add-address'],
      ];
    }

    // Display details about the partnership for information.
    $form['about'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('About the organisation'),
      ],
      '#attributes' => ['class' => 'govuk-form-group'],
      'details' => $this->getDefaultValuesByKey('information', $index, NULL),
    ];
    try {
      $about_edit_link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('edit_comments', 'edit about the organisation');
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($about_edit_link)) {
      $form['about']['edit'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $about_edit_link->toString(),
        '#attributes' => ['class' => 'edit-about-organisation'],
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
