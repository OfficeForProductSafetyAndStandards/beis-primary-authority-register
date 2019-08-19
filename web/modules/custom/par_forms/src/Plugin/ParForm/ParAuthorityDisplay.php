<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Authority detail display, used to highlight information specific to an authority.
 *
 * @ParForm(
 *   id = "authority_display",
 *   title = @Translation("The authority detail display.")
 * )
 */
class ParAuthorityDisplay extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');

    if ($par_data_authority) {
      if ($par_data_authority->hasField('authority_name')) {
        $this->getFlowDataHandler()->setFormPermValue("authority_name", $par_data_authority->get('authority_name')->getString());
      }
      if ($par_data_authority->hasField('ons_code')) {
        $this->getFlowDataHandler()->setFormPermValue("ons_code", $par_data_authority->get('ons_code')->getString());
      }

      if ($par_data_authority->hasField('authority_type')) {
        $this->getFlowDataHandler()->setFormPermValue("authority_type", $par_data_authority->getAuthorityType());
      }

      if ($par_data_authority->hasField('field_regulatory_function')) {
        $regulatory_functions = $par_data_authority->getRegulatoryFunction();
        $authority_functions = [];

        foreach ($regulatory_functions as $regulatory_function) {
          $authority_functions[] = $regulatory_function->label();
        }

        if (!empty($authority_functions)) {
          $this->getFlowDataHandler()
            ->setFormPermValue("regulatory_functions", implode(', ', $authority_functions));
        }
        else {
          $this->getFlowDataHandler()
            ->setFormPermValue("regulatory_functions", $this->t('This authority does not provide any regulatory functions.'));
        }
      }
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    $form['authority'] = [
      '#type' => 'fieldset',
    ];

    $form['authority']['name'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium', 'column-full']],
        '#value' => $this->t('Authority Name'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('authority_name', $cardinality, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getLinkByCurrentOperation('authority_name', $params, [], TRUE);

      $form['authority']['name']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->setText('Change the authority name')->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    $form['authority']['type'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium', 'column-full']],
        '#value' => $this->t('Authority Type'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('authority_type', $cardinality, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getLinkByCurrentOperation('authority_type', $params, [], TRUE);

      $form['authority']['type']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->setText('Change the authority type')->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    $form['authority']['ons'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium', 'column-full']],
        '#value' => $this->t('ONS Code'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('ons_code', $cardinality, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getLinkByCurrentOperation('authority_ons', $params, [], TRUE);

      $form['authority']['ons']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->setText('Change the ons code')->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    $form['authority']['regulatory_functions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium', 'column-full']],
        '#value' => $this->t('Regulatory Functions'),
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-two-thirds']],
        '#value' => $this->getDefaultValuesByKey('regulatory_functions', $cardinality, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getLinkByCurrentOperation('regulatory_functions', $params, [], TRUE);

      $form['authority']['regulatory_functions']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->setText('Change the regulatory functions')->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    return $form;
  }
}
