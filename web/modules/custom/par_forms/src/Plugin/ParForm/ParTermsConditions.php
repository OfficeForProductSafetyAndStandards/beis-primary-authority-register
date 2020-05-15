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
 * Terms & conditions checkbox for confirming partnership details.
 *
 * @ParForm(
 *   id = "terms_and_conditions",
 *   title = @Translation("Terms & Conditions checkbox.")
 * )
 */
class ParTermsConditions extends ParFormPluginBase {

  /**
   * Plugin constants
   */
  const AUTHORITY_TERMS = 'terms_authority_agreed';
  const ORGANISATION_TERMS = 'terms_organisation_agreed';

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $available_formats = [self::AUTHORITY_TERMS, self::ORGANISATION_TERMS];
    $checkbox = isset($this->getConfiguration()['terms']) && array_search($this->getConfiguration()['terms'], $available_formats) !== FALSE
      ? $this->getConfiguration()['terms'] : self::AUTHORITY_TERMS;
    $this->setDefaultValuesByKey("terms", $cardinality, $checkbox);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $url_address = 'https://www.gov.uk/government/publications/primary-authority-terms-and-conditions';
    $url = Url::fromUri($url_address, ['attributes' => ['target' => '_blank']]);
    $terms_link = Link::fromTextAndUrl(t('terms & conditions (opens in a new window)'), $url);

    switch ($this->getFlowDataHandler()->getDefaultValues("terms")) {
      case self::AUTHORITY_TERMS:
        $form[self::AUTHORITY_TERMS] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I have read and agree to the @terms.', ['@terms' => $terms_link->toString()]),
          '#default_value' => $this->getFlowDataHandler()->getDefaultValues(self::AUTHORITY_TERMS),
          '#return_value' => 'on',
        ];

        break;

      case self::ORGANISATION_TERMS:
        $form[self::ORGANISATION_TERMS] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I have read and agree to the @terms.', ['@terms' => $terms_link->toString()]),
          '#default_value' => $this->getFlowDataHandler()->getDefaultValues(self::ORGANISATION_TERMS),
          '#return_value' => 'on',
        ];

        break;
    }

    // Helptext.
    $form['help_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('You won\'t be able to change these details after you save them. Please check everything is correct.'),
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
