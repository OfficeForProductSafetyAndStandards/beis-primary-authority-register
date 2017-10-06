<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\user\Entity\User;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * Class InviteByEmailBlockForm.
 *
 * @package Drupal\invite\Form
 */
class ParPartnershipFlowsInviteForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_invite';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, $par_data_person = NULL) {
    if ($par_data_partnership && $par_data_person) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()},{$par_data_person->id()}");
    }

    if ($par_data_person) {
      // Get the email for the business contact that this email will go to.
      $this->loadDataValue("recipient_email", $par_data_person->get('email')->getString());
      $recipient_exists = $par_data_person->getUserAccount();
      $this->loadDataValue("recipient_exists", !empty($recipient_exists));

      // Get the sender's email and name.
      // For helpdesk users this is a generic title ,
      // and for all other users with a PAR Person record
      // this is tailored to who is inviting.
      $account = User::load($this->currentUser()->id());
      $this->loadDataValue("sender_email", $account->getEmail());
      if($account->hasPermission('invite authority members')) {
        $sender_name = 'BEIS RD Department';
      }
      else {
        $authority = current($par_data_partnership->get('field_authority')->referencedEntities());
        $authority_person = $authority ? $this->getParDataManager()->getUserPerson($account, $authority) : NULL;

        $sender_name = '';
        if (isset($authority_person)) {
          $sender_name = $authority_person->getFullName();
        }
      }
      $this->loadDataValue("sender_name", $sender_name);

      // Get the user accounts related to the business user.
      if ($this->getFlowName() === 'invite_authority_members' && !$recipient_exists) {
        $email_subject = 'New Primary Authority Register';

        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

Primary Authority has been simplified and is now open to all UK businesses.

Simplifying the scheme has required the creation of an entirely new Primary Authority Register in order to accommodate the greater volume of businesses and partnerships.

In order to access the new PA Register, please click on the following link: [invite:invite-accept-link]

After registering, you can continue to access the new PA Register at using the following link: [site:url]

Thanks for your help.
{$sender_name}
HEREDOC;
      }
      elseif ($this->getFlowName() === 'invite_authority_members') {
        $email_subject = 'New Primary Authority Register';

        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

Primary Authority has been simplified and is now open to all UK businesses.

Simplifying the scheme has required the creation of an entirely new Primary Authority Register in order to accommodate the greater volume of businesses and partnerships.

In order to access the new PA Register, please click on the following link: [site:login-url]

After registering, you can continue to access the new PA Register at using the following link: [site:url]

Thanks for your help.
{$sender_name}
HEREDOC;
      }
      elseif ($recipient_exists) {
        $email_subject = 'Invitation to join the Primary Authority Register';

        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

A new partnership has been created for you by {$authority->get('authority_name')->getString()}. Please log in to the Primary Authority Register to update your business's details. To do this, please follow this link:

[site:login-url]

Thanks for your help.
{$sender_name}
HEREDOC;
      }
      else {
        $email_subject = 'Invitation to join the Primary Authority Register';

        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

A new partnership has been created for you by {$authority->get('authority_name')->getString()}. Please create your account with the Primary Authority Register so that you can manage your business's details. To do this, please follow this link:

[invite:invite-accept-link]

Thanks for your help.
{$sender_name}
HEREDOC;
      }

      // Set the default subject for the invite email, this can be changed by the user.
      $this->loadDataValue("email_subject", $email_subject);

      $this->loadDataValue("email_body", $message_body);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_person);

    $invite_type = $this->config('invite.invite_type.invite_organisation_member');
    $data = unserialize($invite_type->get('data'));

    if ($this->getDefaultValues('recipient_exists', FALSE) && $this->getFlowName() === 'invite_authority_members') {
      $form['recipient_exists'] = [
        '#type' => 'markup',
        '#markup' => $this->t('This person has already accepted an invitation, you do not need to re-invite them.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong></p>',
      ];
    }

    // Get Sender.
    if ($this->getFlowName() === 'invite_authority_members') {
      $description = 'You cannot change your email here.';
    }
    else {
      $description = 'You cannot change your email here. If you want to send this invite from a different email address please contact the helpdesk.';
    }
    $form['sender_email'] = [
      '#type' => 'textfield',
      '#title' => t('Your email'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => $this->getDefaultValues('sender_email'),
      '#description' => $description,
    ];
    $form['inviter'] = [
      '#type' => 'hidden',
      '#title' => t('Inviter'),
      '#value' => $this->getCurrentUser()->id(),
    ];

    // Get Recipient.
    if ($this->getFlowName() === 'invite_authority_members') {
      $description = 'This is the contact for the authority.';
      $title = t('Authority contact email');
    }
    else {
      $description = 'This is the businesses primary contact. If you need to send this invite to another person please contact the helpdesk.';
      $title = t('Business contact email');
    }
    $form['recipient_email'] = [
      '#type' => 'textfield',
      '#title' => $title,
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => $this->getDefaultValues('recipient_email'),
      '#description' => $description,
    ];

    // Allow the message subject to be changed.
    $form['email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Message subject'),
      '#default_value' => $this->getDefaultValues('email_subject'),
    ];

    // Allow the message body to be changed.
    $form['email_body'] = [
      '#type' => 'textarea',
      '#rows' => 18,
      '#title' => t('Message'),
      '#default_value' => $this->getDefaultValues('email_body'),
    ];

    // Disable the default 'save' action which takes precedence over 'next' action.
    $this->getFlow()->disableAction('save');

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($par_data_person);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate the form to make sure the correct values have been entered.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('email_subject'))) {
      $form_state->setErrorByName('email_subject', $this->t('<a href="#edit-email-subject">The Message subject is required.</a>'));
    }

    if (empty($form_state->getValue('email_body'))) {
      $form_state->setErrorByName('email_body', $this->t('<a href="#edit-email-body">The Message is required.</a>'));
    }
    // Check that the email body contains an invite accept link.
    $par_data_person = $this->getRouteParam('par_data_person');
    if ($par_data_person->getUserAccount()) {
      $required_token = '[site:login-url]';
    }
    else{
      $required_token = '[invite:invite-accept-link]';
    }
    if (!strpos($form_state->getValue('email_body'), $required_token)) {
      $form_state->setErrorByName('email_body', "Please make sure you have the invite token '$required_token' somewhere in your message.");
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $invite = Invite::create([
      'type' => 'invite_organisation_member',
      'user_id' => $this->getTempDataValue('inviter'),
      'invitee' => $this->getTempDataValue('recipient_email'),
    ]);
    $invite->set('field_invite_email_address', $this->getTempDataValue('recipient_email'));
    $invite->set('field_invite_email_subject', $this->getTempDataValue('email_subject'));
    $invite->set('field_invite_email_body', $this->getTempDataValue('email_body'));
    $invite->setPlugin('invite_by_email');
    if ($invite->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('This invite could not be sent for %person on %form_id');
      $replacements = [
        '%invite' => $this->getTempDataValue('first_name') . ' ' . $this->getTempDataValue('last_name'),
        '%person' => $this->getTempDataValue('recipient_email'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
