<?php

namespace Drupal\par_inspection_feedback_view_flows\Form;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_inspection_feedback_view_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * Reviewing inspection feedback.
 */
class ParFeedbackReplyForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Reply to inspection feedback";

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback');

    $comment = $this->getFlowDataHandler()->getParameter('comment');
    if (!$comment && $par_data_inspection_feedback) {
      $comment = Comment::create([
        'entity_type' => $par_data_inspection_feedback->getEntityTypeId(),
        'entity_id'   => $par_data_inspection_feedback->id(),
        'field_name'  => 'messages',
        'uid' => \Drupal::currentUser()->id(),
        'comment_type' => 'par_inspection_feedback_comments',
        'subject' => substr($par_data_inspection_feedback->label(), 0, 64),
        'status' => 1,
      ]);
    }

    $comment->set('comment_body', $this->getFlowDataHandler()->getTempDataValue('message'));
    $comment->set('field_supporting_document', $this->getFlowDataHandler()->getTempDataValue('files'));

    // Save the inspection feedback.
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
