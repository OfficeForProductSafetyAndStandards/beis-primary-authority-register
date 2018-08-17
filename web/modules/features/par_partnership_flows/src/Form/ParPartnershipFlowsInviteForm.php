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

  /** @var invite type */
  protected $invite_type;

  protected $pageTitle = 'Notify user of partnership invitation';

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, $par_data_person = NULL) {
    // Flows containing the Authority Contact step.
    if (in_array($this->getFlowNegotiator()->getFlowName(), ['invite_authority_members', 'partnership_authority'])) {
      $this->invite_type = "invite_authority_member";
    }

    // Flows containing the Organisation Contact step.
    if (in_array($this->getFlowNegotiator()->getFlowName(), ['partnership_application', 'partnership_direct'])) {
      $this->invite_type = "invite_organisation_member";
    }

    if ($par_data_person) {
      // Get the email for the business contact that this email will go to.
      $this->getFlowDataHandler()->setFormPermValue("recipient_email", $par_data_person->get('email')->getString());
      $recipient_exists = $par_data_person->getUserAccount();
      $this->getFlowDataHandler()->setFormPermValue("recipient_exists", !empty($recipient_exists));

      // Get the sender's email and name.
      // For helpdesk users this is a generic title ,
      // and for all other users with a PAR Person record
      // this is tailored to who is inviting.
      $account = User::load($this->currentUser()->id());
      $this->getFlowDataHandler()->setFormPermValue("sender_email", $account->getEmail());

      $authority = current($par_data_partnership->get('field_authority')->referencedEntities());
      $authority_person = $authority ? $this->getParDataManager()->getUserPerson($account, $authority) : NULL;

      if($account->hasPermission('invite authority members')) {
        $sender_name = 'BEIS RD Department';
      }
      else {
        $sender_name = '';
        if (isset($authority_person)) {
          $sender_name = $authority_person->getFullName();
        }
      }

      $this->getFlowDataHandler()->setFormPermValue("sender_name", $sender_name);

      // Get the user accounts related to the business user.
      if ($this->getFlowNegotiator()->getFlowName() === 'invite_authority_members' && !$recipient_exists) {
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
      elseif ($this->getFlowNegotiator()->getFlowName() === 'invite_authority_members') {
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
        $email_subject = 'Login to view new partnership on Primary Authority Register';

        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

A new partnership has been created for you by {$authority->get('authority_name')->getString()}. Please log in to the Primary Authority Register to update your organisation's details. To do this, please follow this link:

[site:login-url]

Thanks for your help.
{$sender_name}
HEREDOC;
      }
      else {
        $email_subject = 'Invitation to join the Primary Authority Register';

        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

A new partnership has been created for you by {$authority->get('authority_name')->getString()}. Please create your account with the Primary Authority Register so that you can manage your organisation's details. To do this, please follow this link:

[invite:invite-accept-link]

Thanks for your help.
{$sender_name}
HEREDOC;
      }

      // Set the default subject for the invite email, this can be changed by the user.
      $this->getFlowDataHandler()->setFormPermValue("email_subject", $email_subject);

      $this->getFlowDataHandler()->setFormPermValue("email_body", $message_body);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_person);

    $invite_type = $this->config("invite.invite_type.{$this->invite_type}");
    $data = unserialize($invite_type->get('data'));

    if ($this->getFlowDataHandler()->getDefaultValues('recipient_exists', FALSE)) {
      $form['recipient_exists'] = [
        '#type' => 'markup',
        '#markup' => $this->t('This person has already accepted an invitation, you do not need to re-invite them.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong></p>',
      ];
    }

    // Get Sender.
    if ($this->getFlowNegotiator()->getFlowName() === 'invite_authority_members') {
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
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('sender_email'),
      '#description' => $description,
    ];
    $form['inviter'] = [
      '#type' => 'hidden',
      '#title' => t('Inviter'),
      '#value' => $this->getCurrentUser()->id(),
    ];

    // Get Recipient.
    if ($this->invite_type === 'invite_authority_member') {
      $description = $this->t('This is the contact email address for the new authority member/enforcement officer.');
      $title = t('Authority Member email');
    }
    else {
      $description = $this->t('This is the organisation\'s contact email address. If you need to send this invite to another person please contact the helpdesk.');
      $title = t('Organisation Contact email');
    }

    $form['recipient_email'] = [
      '#type' => 'textfield',
      '#title' => $title,
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('recipient_email'),
      '#description' => $description,
    ];

    // Allow the message subject to be changed.
    $form['email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Message subject'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('email_subject'),
    ];

    // Allow the message body to be changed.
    $form['email_body'] = [
      '#type' => 'textarea',
      '#rows' => 18,
      '#title' => t('Message'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('email_body'),
    ];

    // @todo remove this when PAR User Management is complete.
    // Show option to amend role if not already an existing user.
    // This is actually to prevent somebody stripping a user of the authority
    // role until full user management and contacts are on a partnership basis.
    if (!$this->getFlowDataHandler()->getDefaultValues('recipient_exists', FALSE)) {

      foreach (user_roles() as $user_role) {
        if (empty($user_role->get('_core'))) {
          $par_roles[$user_role->id()] = $user_role->label();
        }
      }

      if (!empty($par_roles)) {
        $form['target_role'] = [
          '#type' => 'select',
          '#required' => TRUE,
          '#title' => t('Role'),
          '#description' => t('Choose which role to give this person.'),
          // @todo reduce options.
          '#options' => array_filter($par_roles, function ($role_id) {
            if ($this->invite_type === 'invite_authority_member') {
              return in_array($role_id, ['par_authority', 'par_enforcement']);
            }
            if ($this->invite_type === 'invite_organisation_member') {
              return in_array($role_id, ['par_organisation']);
            }
          }, ARRAY_FILTER_USE_KEY),
          '#default_value' => $data['target_role'],
        ];
      }

    }

    // Disable the default 'save' action which takes precedence over 'next' action.
    $this->getFlowNegotiator()->getFlow()->disableAction('save');

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($par_data_person);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate the form to make sure the correct values have been entered.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (empty($form_state->getValue('recipient_email'))) {
      $id = $this->getElementId(['recipient_email'], $form);
      $form_state->setErrorByName($this->getElementName('recipient_email'), $this->wrapErrorMessage('You must enter the recipient\'s email address.', $id));
    }

    if (empty($form_state->getValue('email_subject'))) {
      $id = $this->getElementId(['email_subject'], $form);
      $form_state->setErrorByName($this->getElementName('email_subject'), $this->wrapErrorMessage('You must enter the subbject for this message.', $id));
    }

    if (empty($form_state->getValue('email_body'))) {
      $id = $this->getElementId(['email_body'], $form);
      $form_state->setErrorByName($this->getElementName('email_body'), $this->wrapErrorMessage('You must enter a message.', $id));
    }
    // Check that the email body contains an invite accept link.
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    if ($par_data_person->getUserAccount()) {
      $required_token = '[site:login-url]';
    }
    else{
      $required_token = '[invite:invite-accept-link]';
    }
    if (!strpos($form_state->getValue('email_body'), $required_token)) {
      $form_state->setErrorByName('email_body', $this->t("<a href=\"#edit-email-body\">Please make sure you have the invite token '@invite_token' somewhere in your message.</a>", ['@invite_token' => $required_token]));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Override invite type if selected the Enforcement Officer role in form.
    if ($this->invite_type === 'invite_authority_member' &&
      $this->getFlowDataHandler()->getTempDataValue('target_role') === 'par_enforcement') {
      $this->invite_type = 'invite_enforcement_officer';
    }

    $invite = Invite::create([
      'type' => $this->invite_type,
      'user_id' => $this->getFlowDataHandler()->getTempDataValue('inviter'),
      'invitee' => $this->getFlowDataHandler()->getTempDataValue('recipient_email'),
    ]);
    $invite->set('field_invite_email_address', $this->getFlowDataHandler()->getTempDataValue('recipient_email'));
    $invite->set('field_invite_email_subject', $this->getFlowDataHandler()->getTempDataValue('email_subject'));
    $invite->set('field_invite_email_body', $this->getFlowDataHandler()->getTempDataValue('email_body'));
    $invite->setPlugin('invite_by_email');
    if ($invite->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('This invite could not be sent for %person on %form_id');
      $replacements = [
        '%invite' => $this->getFlowDataHandler()->getTempDataValue('first_name') . ' ' . $this->getFlowDataHandler()->getTempDataValue('last_name'),
        '%person' => $this->getFlowDataHandler()->getTempDataValue('recipient_email'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
