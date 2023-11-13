<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "messages",
 *   title = @Translation("Messages form.")
 * )
 */
class ParMessageForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    if ($message = $this->getFlowDataHandler()->getParameter('comment')) {
      $message_value = !$message->get('comment_body')->isEmpty() ? $message->get('comment_body')->first()->get('value')->getValue() : '';
      $this->getFlowDataHandler()->setFormPermValue('message', $message_value);
      $this->getFlowDataHandler()->setFormPermValue('files', $message->get('field_supporting_document')->getString());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Submit a response'),
      '#title_tag' => 'h2',
      '#default_value' => $this->getDefaultValuesByKey('message', $index),
      '#description' => 'Use this section to respond to the original query.',
    ];

    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload supporting documents (optional)'),
      '#title_tag' => 'h2',
      '#description' => t('Use Ctrl or cmd to select multiple files'),
      '#upload_location' => 's3private://documents/messages/',
      '#multiple' => TRUE,
      '#default_value' => $this->getDefaultValuesByKey("files", $index),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps'
        ],
      ],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $response_element = $this->getElement($form, ['message'], $index);
    $response = $response_element ? $form_state->getValue($response_element['#parents']) : NULL;

    if (empty($response)) {
      $message = 'You must enter a response.';
      $this->setError($form, $form_state, $response_element, $message);
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
