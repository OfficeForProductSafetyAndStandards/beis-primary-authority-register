<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Organisation detail display, used to highlight information specific to an organisation.
 *
 * @ParForm(
 *   id = "organisation_display",
 *   title = @Translation("The organisation detail display.")
 * )
 */
class ParOrganisationDisplay extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');

    if ($par_data_organisation) {
      if ($par_data_organisation->hasField('organisation_name')) {
        $this->getFlowDataHandler()->setFormPermValue("organisation_name", $par_data_organisation->get('organisation_name')->getString());
      }
      if ($par_data_organisation->hasField('comments')) {
        $this->getFlowDataHandler()->setFormPermValue("about_business", $par_data_organisation->get('comments')->getString());
      }

      if ($par_data_organisation->hasField('trading_name')) {
        $this->getFlowDataHandler()->setFormPermValue("trading_names", $par_data_organisation->getTradingNames());
      }

      if ($par_data_organisation->hasField('field_sic_code')) {
        $sic_codes = $par_data_organisation->getSicCode();
        $organisation_codes = [];

        foreach ($sic_codes as $sic_code) {
          $organisation_codes[] = $sic_code->label();
        }

        if (!empty($organisation_codes)) {
          $this->getFlowDataHandler()
            ->setFormPermValue("sic_codes", implode(', ', $organisation_codes));
        }
        else {
          $this->getFlowDataHandler()
            ->setFormPermValue("sic_codes", $this->t('This organisation does not provide any SIC codes.'));
        }
      }

      if ($par_data_organisation->hasField('field_legal_entity')) {
        $legal_entities = [];

        foreach ($par_data_organisation->getLegalEntity() as $legal_entity) {
          $legal_entities[$legal_entity->uuid()] = "{$legal_entity->label()} ({$legal_entity->getRegisteredNumber()}), {$legal_entity->getType()}";
        }

        if (!empty($legal_entities)) {
          $this->getFlowDataHandler()
            ->setFormPermValue("legal_entities", implode(', ', $legal_entities));
        }
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    $form['organisation'] = [
      '#type' => 'container',
    ];

    $form['organisation']['name'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m', 'govuk-grid-column-full']],
        '#value' => $this->t('Organisation Name'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('organisation_name', $index, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('organisation_name', 'Change the organisation name', $params);
      $form['organisation']['name']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    $form['organisation']['about'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m', 'govuk-grid-column-full']],
        '#value' => $this->t('About the organisation'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('about_business', $index, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('organisation_about', 'Change the description', $params);
      $form['organisation']['about']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    $form['organisation']['trading_name'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m', 'govuk-grid-column-full']],
        '#value' => $this->t('Trading names'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('trading_names', $index, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('trading_name', 'Change the trading names', $params);
      $form['organisation']['trading_name']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    $form['organisation']['sic_codes'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m', 'govuk-grid-column-full']],
        '#value' => $this->t('SIC codes'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('sic_codes', $index, NULL),
      ],
    ];

    // Add operation link for updating sic codes.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('sic_codes', 'Change the SIC codes', $params);
      $form['organisation']['sic_codes']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    $form['organisation']['legal_entities'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m', 'govuk-grid-column-full']],
        '#value' => $this->t('Legal entities'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds', 'form-hint']],
        '#value' => 'Legal entities cannot be updated for existing organisations.',
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['govuk-grid-column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('legal_entities', $index, NULL),
      ],
    ];

    return $form;
  }

}
