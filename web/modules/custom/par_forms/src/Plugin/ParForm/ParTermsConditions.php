<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_forms\Annotation\ParForm;
use Drupal\par_forms\ParFormBuilder;
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

    $config = $this->getConfiguration();

    $url_address = 'https://www.gov.uk/government/publications/primary-authority-terms-and-conditions';
    $url = Url::fromUri($url_address, ['attributes' => ['target' => '_blank']]);
    $terms_link = Link::fromTextAndUrl(t('terms & conditions (opens in a new window)'), $url);

    switch ($this->getFlowDataHandler()->getDefaultValues("terms")) {
      case self::AUTHORITY_TERMS:
        $form[self::AUTHORITY_TERMS] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I have read and agree to the @terms.', ['@terms' => $terms_link->toString()]),
          '#default_value' => 0,
          '#return_value' => 'on',
        ];

        break;

      case self::ORGANISATION_TERMS:
        $form[self::ORGANISATION_TERMS] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I have read and agree to the @terms.', ['@terms' => $terms_link->toString()]),
          '#default_value' => 0,
          '#return_value' => 'on',
        ];

        break;
    }

    // Helptext.
    if (!empty($config['help_paras'])) {
      $form['help_text'] = [
        '#type' => 'container',
      ];
      foreach ($config['help_paras'] as $i => $para) {
        $form['help_text'][$i] = [
          '#type' => 'markup',
          '#markup' => $this->t($para),
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
    }

    return $form;
  }

  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    parent::validate($form, $form_state, $cardinality, $action);

    if (empty($form_state->getValue('terms_authority_agreed'))) {
      $id = $this->getElementId(['terms_authority_agreed'], $form);
      $form_state->setErrorByName($this->getElementName('terms_authority_agreed'), $this->wrapErrorMessage('You must agree to the terms and conditions.', $id));
    }
  }
}
