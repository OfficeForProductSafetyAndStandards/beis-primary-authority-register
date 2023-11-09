<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "message_detail",
 *   title = @Translation("Message detail display form.")
 * )
 */
class ParMessageDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $messages = $this->getFlowDataHandler()->getParameter('comments');

    $delta = $index - 1;

    // Cardinality is not a zero-based index like the stored fields deltas.
    $message = $messages[$delta] ?? NULL;

    if ($message instanceof CommentInterface) {
      $comment_entity = $message->getCommentedEntity();

      if ($comment_entity instanceof ParDataEntityInterface) {
        $primary_authority = $comment_entity->getPrimaryAuthority(TRUE);
        $enforcing_authority = $comment_entity->getEnforcingAuthority(TRUE);

        if ($primary_authority_person = $this->getParDataManager()->getUserPerson($message->getOwner(), $primary_authority)) {
          $this->setDefaultValuesByKey("author", $index, "{$primary_authority_person->getFullName()} (Primary Authority Officer)");
        }
        elseif ($enforcing_authority_person = $this->getParDataManager()->getUserPerson($message->getOwner(), $enforcing_authority)) {
          $this->setDefaultValuesByKey("author", $index, "{$enforcing_authority_person->getFullName()} (Enforcing Officer)");
        }
        else {
          $this->setDefaultValuesByKey("author", $index, $message->getOwner()->label());
        }
      }

      $date = $this->getDateFormatter()->format($message->getCreatedTime(), 'gds_date_format');
      $this->setDefaultValuesByKey("date", $index, $date);

      if ($message->hasField('comment_body')) {
        $this->setDefaultValuesByKey("message", $index, $message->comment_body->view('full'));
      }
      if ($message->hasField('field_supporting_document')) {
        $this->setDefaultValuesByKey("files", $index, $message->field_supporting_document->view('full'));
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    if ($index === 1) {
      $form['message_intro'] = [
        '#type' => 'container',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Responses'),
          '#attributes' => ['class' => ['govuk-heading-l']],
        ],
        '#attributes' => ['class' => ['govuk-form-group']],
      ];

      // Add operation link for replying to request.
      try {
        $link = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('reply', 'Submit a response', $params);
        $form['message_intro']['reply'] = [
          '#type' => 'markup',
          '#weight' => 99,
          '#markup' => t('@link', [
            '@link' => $link ? $link->toString() : '',
          ]),
        ];
      }
      catch (ParFlowException $e) {

      }
    }

    if ($this->getDefaultValuesByKey('message', $index, NULL)) {
      $form['message'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group', 'panel panel-border-wide']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => "Submitted by {$this->getDefaultValuesByKey('author', $index, NULL)} on {$this->getDefaultValuesByKey('date', $index, NULL)}",
        ],
        'message' => $this->getDefaultValuesByKey('message', $index, NULL),
        'document' => $this->getDefaultValuesByKey('files', $index, NULL),
      ];
    }
    else {
      $form['message'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('There are no responses yet.'),
        ],
      ];
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions(array $actions = [], array $data = NULL): ?array {
    return $actions;
  }
}
