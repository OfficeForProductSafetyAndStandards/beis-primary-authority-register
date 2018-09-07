<?php

namespace Drupal\par_enquiry_view_flows\Form;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_enquiry_view_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * Replying to enquiry.
 */
class ParEnquiryReplyForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Reply to enquiry";

  /**
   * @return CacheTagsInvalidatorInterface
   */
  public function getCacheTagsInvalidator() {
    return \Drupal::service('cache_tags.invalidator');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_general_enquiry = $this->getFlowDataHandler()->getParameter('par_data_general_enquiry');

    $comment = $this->getFlowDataHandler()->getParameter('comment');
    if (!$comment && $par_data_general_enquiry) {
      $comment = Comment::create([
        'entity_type' => $par_data_general_enquiry->getEntityTypeId(),
        'entity_id'   => $par_data_general_enquiry->id(),
        'field_name'  => 'messages',
        'uid' => \Drupal::currentUser()->id(),
        'comment_type' => 'par_general_enquiry_comments',
        'subject' => substr($par_data_general_enquiry->label(), 0, 64),
        'status' => 1,
      ]);
    }

    $comment->set('comment_body', $this->getFlowDataHandler()->getTempDataValue('message'));
    $comment->set('field_supporting_document', $this->getFlowDataHandler()->getTempDataValue('files'));

    // Save the inspection feedback.
    if ($comment && $comment->save()) {
      $this->getFlowDataHandler()->deleteStore();
      $tags = ["{$par_data_general_enquiry->getEntityTypeId()}:{$par_data_general_enquiry->id()}"];
      $this->getCacheTagsInvalidator()->invalidateTags($tags);
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
