<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Url;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    $account = $this->getFlowDataHandler()->getParameter('user');
    if ($account && ('1' === $account->get('field_gdpr')->getString() || $account->get('field_gdpr')->getString() === TRUE)) {
      $this->setDefaultValuesByKey("gdpr_agreement", $cardinality, TRUE);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // If this notice has already been reviewed then skip this form.
    if ($this->getDefaultValuesByKey('gdpr_agreement', $cardinality, FALSE)) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute(), $this->getRouteParams());
      return new RedirectResponse($url);
    }

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
      '#type' => 'checkbox',
      '#title' => $this->t('Please confirm you have read the Privacy Notice and understand how the Office intends to use your personal data'),
      '#return_value' => 'on',
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $data_policy_key = $this->getElementKey('data_policy');
    if ($form_state->getValue($data_policy_key) !== 'on') {
      $id_key = $this->getElementKey('data_policy', $cardinality, TRUE);
      $message = $this->wrapErrorMessage('Please confirm you have read the Privacy Notice and understand how the Office intend to use your personal data.', $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($data_policy_key), $message);
    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
