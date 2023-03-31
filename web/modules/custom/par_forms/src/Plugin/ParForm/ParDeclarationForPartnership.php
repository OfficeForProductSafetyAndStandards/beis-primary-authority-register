<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\par_forms\Annotation\ParForm;
use Drupal\par_forms\ParFormBuilder;
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

    $form['declaration'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Please confirm that:'),
    );

    // Conditions text.
    $form['declaration']['conditions_list'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet'],],
      '#context' => ['list_style' => 'bullet'],
      '#items' => [
        Markup::create('the organisation is eligible to enter into a partnership'),
        Markup::create('your local authority is suitable for nomination as primary authority for the organisation'),
        Markup::create('you have notified the organisation that any other authorities that currently regulate ' .
                       'it will continue to do so after this partnership is created'),
        Markup::create('a written summary of partnership arrangements has been agreed with the organisation'),
        Markup::create('your local authority agrees to the ' . $terms_link->toString()),
      ],
    ];

    // Check box.
    $form['declaration'][self::DECLARATION] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm these conditions have been met'),
      '#default_value' => 0,
      '#return_value' => 'on',
    ];

    // Explanation text.
    $form['declaration']['explanation_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('These essential conditions for the continuance of the partnership are required by ' .
                            'the Regulatory Enforcement and Sanctions Act 2008 (as amended by the Enterprise Act ' .
                            '2016) and the Primary Authority Statutory Guidance.'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Confirm');

    return $form;
  }

  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    parent::validate($form, $form_state, $cardinality, $action);

    $checked = $form_state->getValue(self::DECLARATION, 0);
    if (!$checked) {
      $id = $this->getElementId([self::DECLARATION], $form);
      $form_state->setErrorByName($this->getElementName(self::DECLARATION), $this->wrapErrorMessage('You must confirm that the conditions of the declaration are met.', $id));
    }
  }
}
