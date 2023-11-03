<?php

namespace Drupal\par_notification\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\invite\Entity\Invite;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\login_destination\Entity\LoginDestination;
use Drupal\Core\Url;

/**
 * The invitation form for new users.
 */
class ParInvitationForm extends FormBase {

  /**
   * The message fields which contain the primary entity context.
   */
  protected $primary_entity_fields = [
    'field_partnership',
    'field_enforcement_notice',
    'field_deviation_request',
    'field_general_enquiry',
    'field_inspection_feedback',
    'field_inspection_plan'
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_notification_invite';
  }

  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, MessageInterface $message = NULL) {
    // Need to check to see if the user has an account already.
    $message = \Drupal::routeMatch()->getParameter('message');

    $email = $message->hasField('field_to') && !$message->get('field_to')->isEmpty() ?
      $message->get('field_to')->getString() : NULL;

    $existing_account = $email ?
      user_load_by_mail($email) : NULL;

    // Invitations can only be issued if a user account does not exist.
    if ($existing_account) {
      $form['intro'] = [
        '#markup' => $this->t('<p>An account already exists for @email, please try resetting your password.</p>', ['@email' => $email]),
      ];
      return $form;
    }
    // Invitations can only be issued if the message supports invitations.
    elseif (!$this->getInvitationType($message)) {
      $form['intro'] = [
        '#markup' => $this->t('<p>This link does not support requesting an invitation, please contact OPSS at <a href="mailto:pa@beis.gov.uk">pa@beis.gov.uk</a> if you need assistance.</p>'),
      ];
      return $form;
    }

    $form['#form_id'] = $this->getFormId();

    $form['intro'] = [
      ['#markup' => t('<p>You will be sent an invitation to verify your email address.</p>')],
      ['#markup' => t('<p>Please follow the invitation link in the email to create your account.</p>')],
      ['#markup' => t('<p>Once you have created your account you will be able to access this link.</p>')],
    ];

    // Set the email to be invited.
    $form['email'] = [
      '#type' => 'hidden',
      '#value' => $email,
    ];

    // Set the email to be invited.
    $form['actions']['refresh'] = [
      '#type' => 'submit',
      '#name' => 'refresh',
      '#value' => "Show the link",
      '#submit' => ['::refreshPage'],
      '#limit_validation_errors' => [],
      '#attributes' => [
        'class' => ['flow-link', 'govuk-button', 'govuk-button--secondary'],
        'onclick' =>  "location.reload();",
      ],
    ];

    $form['actions']['send'] = [
      '#type' => 'submit',
      '#name' => 'invite',
      '#value' => $this->t('Send invitation'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    if (empty($email)) {
      $form_state->setErrorByName('email', $this->t('<a href="#edit-email">The @field is required.</a>', ['@field' => $form['email']['#title']]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submit_action = $form_state->getTriggeringElement()['#name'];

    // Send invitation.
    if ($submit_action === 'invite') {
      /** @var MessageInterface $message */
      $message = \Drupal::routeMatch()->getParameter('message');

      try {
        $email = $form_state->getValue('email');
        $subject = $this->getInvitationSubject($message);
        $body = $this->getInvitationBody($message);

        $invite = Invite::create([
          'type' => self::getInvitationType($message),
          'user_id' => $message->getOwnerId(),
          'invitee' => $email,
        ]);

        $invite->set('field_invite_email_address', $email);
        $invite->set('field_invite_email_subject', $subject);
        $invite->set('field_invite_email_body', $body);
        $invite->setPlugin('invite_by_email');

        $invite->save();
      }
      catch (\Exception $e) {
        $this->messenger()->addMessage("Error occurred executing invitation: {$e->getMessage()}", 'error');
      }
    }
  }

  /**
   * Get the invitation type from the message.
   *
   * Some messages are sent to multiple people,
   * contextual information added to the message
   * must be used to identify the user's role.
   *
   * @return string
   *  The invitiation type to be created.
   */
  public function getInvitationType(MessageInterface $message) {
    // @TODO PAR-1736: Add invitations for more message types.
    switch ($message->getTemplate()->id()) {
      case 'approved_enforcement':
        return 'invite_organisation_member';

        break;

    }

    return NULL;
  }

  /**
   * Get the person related to this invitation.
   */
  public function getPerson(MessageInterface $message) {
    $email = $message->hasField('field_to') && !$message->get('field_to')->isEmpty() ?
      $message->get('field_to')->getString() : NULL;

    if ($email && $primary_entity = $this->getPrimaryEntity($message)) {
      $related_entities = $this->getParDataManager()->getRelatedEntities($primary_entity);

      $people = array_filter($related_entities, function ($entity) use ($email) {
        return ($entity instanceof ParDataPersonInterface
          && $entity->getEmail() === $email);
      });

      // @TODO this doesn't necessarily return the most related person,
      // if there are multiple entities it will just return the first one found.
      return current($people);
    }

    return NULL;
  }

  /**
   * Get the primary entity for the message.
   */
  public function getPrimaryEntity(MessageInterface $message) {
    foreach ($this->primary_entity_fields as $field) {
      if ($message->hasField($field) && !$message->get($field)->isEmpty()) {
        return current($message->get($field)->referencedEntities());
      }
    }

    return NULL;
  }

  /**
   * Get the subject for the invitation.
   */
  public function getInvitationSubject(MessageInterface $message) {
    return 'Invitation to join the Primary Authority Register';
  }

  /**
   * Get the body for the invitation.
   */
  public function getInvitationBody(MessageInterface $message) {
    $person = $this->getPerson($message);
    $name = $person ? $person->getFirstName() : 'Primary Authority User';

    $body = <<<HEREDOC
Dear {$name},

You requested an invitation to sign up for the Primary Authority Register, please follow the invitation link to create your account:

[invite:invite-accept-link]

If you did not request an invitation please ignore this email or contact OPSS at pa@beis.gov.uk to discuss this further.

Thanks for your help.
Primary Authority Team
HEREDOC;

    return $body;
  }

}
