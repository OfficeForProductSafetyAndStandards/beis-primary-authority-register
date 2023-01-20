<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Declaration for partnership checkbox for confirming authority to authorise partnership.
 *
 * @ParForm(
 *   id = "declaration_for_partnership",
 *   title = @Translation("Declaration for Partnership checkbox.")
 * )
 */
class ParDeclarationForPartnership extends ParFormPluginBase {

  /**
   * Plugin constants
   */
  const DECLARATION = 'declaration_agreed';

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $checkbox = isset($this->getConfiguration()['declaration']) && $this->getConfiguration()['declaration'] == self::DECLARATION
      ? $this->getConfiguration()['declaration'] : self::DECLARATION;
    $this->setDefaultValuesByKey("declaration", $cardinality, $checkbox);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $url_address = 'https://www.gov.uk/government/publications/primary-authority-terms-and-conditions';
    $url = Url::fromUri($url_address, ['attributes' => ['target' => '_blank']]);
    $terms_link = Link::fromTextAndUrl(t('terms & conditions (opens in a new window)'), $url);

    // Conditions text.
    $form['conditions_text'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => '<h2 class="govuk-heading-m">Please confirm that:</h2>',
      '#items' => [
        'the organisation is eligible to enter into a partnership',
        'your local authority is suitable for nomination as primary authority for the organisation',
        'you have notified the organisation that any other authorities that currently regulate it will continue to do so after this partnership is created',
        'a written summary of partnership arrangements has been agreed with the organisation',
        'your local authority agrees to the ' . $terms_link->toString(),
      ],
    ];

    // Check box.
    $form[self::DECLARATION] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm these conditions have been met'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues(self::DECLARATION),
      '#return_value' => 'on',
    ];

    // Explanation text.
    $form['explanation_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('These essential conditions for the continuance of the partnership are required by ' .
                            'the Regulatory Enforcement and Sanctions Act 2008 (as amended by the Enterprise Act ' .
                            '2016) and the Primary Authority Statutory Guidance.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

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
