<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Url;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "data_policy",
 *   title = @Translation("GDPR data policy form.")
 * )
 */
class ParGdprForm extends ParFormPluginBase {

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['notice'] = [
      '#type' => 'markup',
      '#markup' => "This privacy notice will help you to understand what personal data the Office collects about you, how the Office uses this personal data, and what rights you have regarding your personal data.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => "It is important that you read this notice, together with any other privacy notice that is provided to you on specific occasions when we are collecting or processing your personal data, so that you are aware of how and why we are using it.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['privacy_notice'] = [
      '#type' => 'link',
      '#title' => $this->t('Link to full privacy notice'),
      '#url' => Url::fromUri('https://www.gov.uk/government/uploads/system/uploads/attachment_data/file/711378/safety-and-standards-gdpr-privacy-notice.pdf'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['summary'] = [
      '#theme' => 'item_list',
      '#title' => $this->t('Your personal information is used to'),
      '#items' => [
        'notify you of any updates to partnerships you have control of',
        'allow other users to enquire about any of these partnerships',
        'record ownership of any enforcements you raise against another partnership',
        'notify you about any changes to these enforcements'
      ],
      '#attributes' => ['class' => ['list', 'form-group', 'list-bullet']],
    ];

    $form['data_policy'] = [
      '#type' => 'markup',
      '#markup' => 'By continuing you agree to this privacy notice, if you would like to opt-out please <a href="mailto:pa@beis.gov.uk" target="_blank">contact the helpdesk</a>.',
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    return $form;
  }
}
