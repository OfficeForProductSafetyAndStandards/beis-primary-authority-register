<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  public function loadData(int $index = 1): void {
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

      if ($address = $par_data_authority->getPremises(TRUE)) {
        $this->getFlowDataHandler()->setFormPermValue("authority_address", $address->label());
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

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
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
        '#value' => $this->getDefaultValuesByKey('authority_name', $index, NULL),
      ],
    ];
    $params = $this->getRouteParams() + ['destination' => $return_path];
    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('authority_name', 'Change the authority name', $params);

      $form['authority']['name']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
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
        '#value' => $this->getDefaultValuesByKey('authority_type', $index, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('authority_type', 'Change the authority type', $params);

      $form['authority']['type']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    if ($primary_address = $this->getDefaultValuesByKey('authority_address', $index, NULL)) {
      $form['authority']['address'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['grid-row']],
        'heading' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#attributes' => ['class' => ['heading-medium', 'column-full']],
          '#value' => $this->t('Primary Address'),
        ],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#attributes' => ['class' => ['column-two-thirds']],
          '#value' => $primary_address,
        ],
      ];

      // Add operation link for updating authority details.
      try {
        $link = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('authority_address', 'Change the primary address', $params);

        $form['authority']['address']['change'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#attributes' => ['class' => ['column-one-third']],
          '#weight' => 99,
          '#value' => t('@link', [
            '@link' => $link ? $link->toString() : '',
          ]),
        ];
      } catch (ParFlowException $e) {

      }
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
        '#value' => $this->getDefaultValuesByKey('ons_code', $index, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('authority_ons', 'Change the ons code', $params);

      $form['authority']['ons']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
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
        '#value' => $this->getDefaultValuesByKey('regulatory_functions', $index, NULL),
      ],
    ];

    // Add operation link for updating authority details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('regulatory_functions', 'Change the regulatory functions', $params);

      $form['authority']['regulatory_functions']['change'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => ['column-one-third']],
        '#weight' => 99,
        '#value' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    return $form;
  }
}
