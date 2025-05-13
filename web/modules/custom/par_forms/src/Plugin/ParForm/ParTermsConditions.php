<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Link;
use Drupal\Core\Url;
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
   * Plugin constants.
   */
  const AUTHORITY_TERMS = 'terms_authority_agreed';
  const ORGANISATION_TERMS = 'terms_organisation_agreed';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $available_formats = [self::AUTHORITY_TERMS, self::ORGANISATION_TERMS];
    $checkbox = isset($this->getConfiguration()['terms']) && array_search($this->getConfiguration()['terms'], $available_formats) !== FALSE
      ? $this->getConfiguration()['terms'] : self::AUTHORITY_TERMS;
    $this->setDefaultValuesByKey("terms", $index, $checkbox);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {

    switch ($this->getFlowDataHandler()->getDefaultValues("terms")) {
      case self::AUTHORITY_TERMS:
        $form[self::AUTHORITY_TERMS] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I have read and agree to the terms and conditions.'),
          '#default_value' => $this->getFlowDataHandler()->getDefaultValues(self::AUTHORITY_TERMS),
          '#return_value' => 'on',
          '#wrapper_attributes' => ['class' => ['govuk-!-margin-top-4']],
        ];

        break;

      case self::ORGANISATION_TERMS:
        $form[self::ORGANISATION_TERMS] = [
          '#type' => 'checkbox',
          '#title' => $this->t('I have read and agree to the terms and conditions.'),
          '#default_value' => $this->getFlowDataHandler()->getDefaultValues(self::ORGANISATION_TERMS),
          '#return_value' => 'on',
          '#wrapper_attributes' => ['class' => ['govuk-!-margin-top-4']],
        ];

        break;
    }

    // Terms & conditions.
    $url_address = 'https://www.gov.uk/government/publications/primary-authority-terms-and-conditions';
    $url = Url::fromUri($url_address, ['attributes' => ['target' => '_blank']]);
    $terms_link = Link::fromTextAndUrl(t('Terms & conditions (opens in a new window)'), $url);
    $form['terms_link'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      [
        '#type' => 'link',
        '#title' => $terms_link->getText(),
        '#url' => $terms_link->getUrl(),
        '#options' => $terms_link->getUrl()->getOptions(),
      ],
      '#attributes' => ['class' => ['govuk-!-margin-bottom-4']],
    ];

    // Helptext.
    $form['help_text'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('You won\'t be able to change these details after you save them. Please check everything is correct.'),
    ];

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getComponentActions(array $actions = [], ?array $data = NULL): ?array {
    return $actions;
  }

}
