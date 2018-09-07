<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $messages = $this->getFlowDataHandler()->getParameter('comments');
    // Cardinality is not a zero-based index like the stored fields deltas.
    $message = isset($messages[$cardinality-1]) ? $messages[$cardinality-1] : NULL;

    if ($message instanceof CommentInterface) {
      $comment_entity = $message->getCommentedEntity();

      if ($comment_entity instanceof ParDataEntityInterface) {
        $primary_authority = $comment_entity->getPrimaryAuthority(TRUE);
        $enforcing_authority = $comment_entity->getEnforcingAuthority(TRUE);

        if ($primary_authority_person = $this->getParDataManager()->getUserPerson($message->getOwner(), $primary_authority)) {
          $this->setDefaultValuesByKey("author", $cardinality, "{$primary_authority_person->getFullName()} (Primary Authority Officer)");
        }
        elseif ($enforcing_authority_person = $this->getParDataManager()->getUserPerson($message->getOwner(), $enforcing_authority)) {
          $this->setDefaultValuesByKey("author", $cardinality, "{$enforcing_authority_person->getFullName()} (Enforcing Officer)");
        }
        else {
          $this->setDefaultValuesByKey("author", $cardinality, $message->getOwner()->label());
        }
      }

      $date = $this->getDateFormatter()->format($message->getCreatedTime(), 'gds_date_format');
      $this->setDefaultValuesByKey("date", $cardinality, $date);

      if ($message->hasField('comment_body')) {
        $this->setDefaultValuesByKey("message", $cardinality, $message->comment_body->view('full'));
      }
      if ($message->hasField('field_supporting_document')) {
        $this->setDefaultValuesByKey("files", $cardinality, $message->field_supporting_document->view('full'));
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    if ($cardinality === 1) {
      $form['message_intro'] = [
        '#type' => 'fieldset',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Replies to the deviation request'),
          '#attributes' => ['class' => ['heading-large']],
        ],
        '#attributes' => ['class' => ['form-group']],
      ];

      // Add operation link for replying to request.
      try {
        $form['message_intro']['reply'] = [
          '#type' => 'markup',
          '#weight' => 99,
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()
              ->getLinkByCurrentOperation('reply', $params, [])
              ->setText('Submit a reply')
              ->toString(),
          ]),
        ];
      }
      catch (ParFlowException $e) {

      }
    }

    if ($this->getDefaultValuesByKey('message', $cardinality, NULL)) {
      $form['message'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group', 'panel panel-border-wide']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => "Submitted by {$this->getDefaultValuesByKey('author', $cardinality, NULL)} on {$this->getDefaultValuesByKey('date', $cardinality, NULL)}",
        ],
        'message' => $this->getDefaultValuesByKey('message', $cardinality, NULL),
        'document' => $this->getDefaultValuesByKey('files', $cardinality, NULL),
      ];
    }
    else {
      $form['message'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('There are no replies yet.'),
        ],
      ];
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($cardinality = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}
