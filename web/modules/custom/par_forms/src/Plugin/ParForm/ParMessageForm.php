<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParEntityMapping;
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
      '#default_value' => $this->getDefaultValuesByKey('message', $index),
      '#description' => '<p>Use this section to respond to the original query.</p>',
    ];

    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload supporting documents (optional)'),
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
}
