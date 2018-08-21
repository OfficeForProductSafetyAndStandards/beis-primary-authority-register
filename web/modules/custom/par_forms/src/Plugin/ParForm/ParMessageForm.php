<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
  public function loadData($cardinality = 1) {
    if ($message = $this->getFlowDataHandler()->getParameter('comment')) {
      $this->getFlowDataHandler()->setFormPermValue('message', $message->get('comment_body')->getString());
      $this->getFlowDataHandler()->setFormPermValue('files', $message->get('field_supporting_document')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Submit a response'),
      '#default_value' => $this->getDefaultValuesByKey('message', $cardinality),
      '#description' => '<p>Use this section to respond to the original query.</p>',
    ];

    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload supporting documents (optional)'),
      '#description' => t('Use Ctrl or cmd to select multiple files'),
      '#upload_location' => 's3private://documents/messages/',
      '#multiple' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("files"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps'
        ],
      ],
    ];

    return $form;
  }
}
