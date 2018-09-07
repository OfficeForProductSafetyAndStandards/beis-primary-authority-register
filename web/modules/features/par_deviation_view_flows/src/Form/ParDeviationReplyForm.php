<?php

namespace Drupal\par_deviation_view_flows\Form;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_deviation_view_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * Reviewing a deviation request.
 */
class ParDeviationReplyForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Reply to deviation request";

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_deviation_request = $this->getFlowDataHandler()->getParameter('par_data_deviation_request');

    $comment = $this->getFlowDataHandler()->getParameter('comment');
    if (!$comment && $par_data_deviation_request) {
      $comment = Comment::create([
        'entity_type' => $par_data_deviation_request->getEntityTypeId(),
        'entity_id'   => $par_data_deviation_request->id(),
        'field_name'  => 'messages',
        'uid' => \Drupal::currentUser()->id(),
        'comment_type' => 'par_deviation_request_comments',
        'subject' => substr($par_data_deviation_request->label(), 0, 64),
        'status' => 1,
      ]);
    }

    $comment->set('comment_body', $this->getFlowDataHandler()->getTempDataValue('message'));
    $comment->set('field_supporting_document', $this->getFlowDataHandler()->getTempDataValue('files'));

    // Save the deviation request.
    if ($comment && $comment->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The message %confirm could not be saved');
      $replacements = [
        '%confirm' => $comment->id(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }
}
