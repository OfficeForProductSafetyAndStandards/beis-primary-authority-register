<?php

namespace Drupal\par_flow_helpdesk\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\user\Entity\User;

/**
 * Class InviteByEmailBlockForm.
 *
 * @package Drupal\par_flow_helpdesk\Form
 */
class ParFlowHelpdeskInviteForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'helpdesk';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_helpdesk_invite_authority';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPerson $par_data_person
   *   The Person being invited.
   */
  public function retrieveEditableValues($par_data_person = NULL) {
    if ($par_data_person) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("invite:{$par_data_person->id()}");

      // Set the default subject for the invite email, this can be changed by the user.
      $this->loadDataValue("email_subject", 'Primary Authority is changing - here\'s what you need to do');

      $this->loadDataValue("authority_member", $par_data_person->retrieveStringValue('email'));

      $message_body = <<<HEREDOC
Primary Authority is changing – here’s what you need to do.

We’re making changes to the way Primary Authority works.  As part of this we are improving the Primary Authority Register and the link below will take you to the first stage of this process. Here you can transition all your details to the new Register. 
This is the first iteration of the new system, and we’re keen to get your feedback. 

Some of the rules that govern your Primary Authority partnerships will also change. You’ll need to confirm that you agree to the new scheme rules, and also update your existing data in the Primary Authority Register so that it is correct when the new site goes live. 
Update your data now:

[invite:invite-accept-link]

This link can only be used once to log in and will lead you to a page where you can set your password.

What we need you to do:
--Review and accept the updated terms and conditions
--Check all of the documents you manage in the register are up to date
--Work with your business partners to make sure they also complete the steps above

For more information, please read the new statutory guidance.

Regulatory Delivery team
If you need assistance call the Help Desk 0121 345 1201 or email pa@beis.gov.uk
HEREDOC;
      $this->loadDataValue("email_body", $message_body);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_person);

    $invite_type = $this->config('invite.invite_type.invite_organisation_member');
    $data = unserialize($invite_type->get('data'));

    $form['leading_paragraph'] = [
      '#type' => 'markup',
      '#markup' => t('<p>Review and confirm your data by 14 September 2017</p>'),
    ];

    // Get Sender.
    $form['authority_member'] = [
      '#type' => 'textfield',
      '#title' => t('Your email'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => $this->currentUser->getEmail(),
      '#description' => 'You cannot change your email here. If you want to send this invite from a different email address please contact the helpdesk.',
    ];
    $form['inviter'] = [
      '#type' => 'hidden',
      '#title' => t('Inviter'),
      '#value' => $this->currentUser->id(),
    ];

    // Get Recipient.
    $form['authority_member'] = [
      '#type' => 'textfield',
      '#title' => t('Authority contact email'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => $this->getDefaultValues('authority_member'),
      '#description' => 'This is the authority contact and cannot be changed here. If you need to send this invite go back to the <a href="/dv/rd-dashboard">helpdesk dashboard</a>.',
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

    $form['send'] = [
      '#type' => 'submit',
      '#value' => t('Send invite'),
    ];

    $previous_link = $this->getFlow()->getLinkByStep(1)->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $previous_link]),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_person);

    return $form;
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
    if (!strpos($form_state->getValue('email_body'), '[invite:invite-accept-link]')) {
      $form_state->setErrorByName('email_body', "Please make sure you have the invite token '[invite:invite-accept-link]' somewhere in your message.");
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $invite = Invite::create([
      'type' => 'invite_authority_member',
      'user_id' => $this->getTempDataValue('inviter'),
      'invitee' => $this->getTempDataValue('authority_member'),
    ]);
    $invite->set('field_invite_email_address', $this->getTempDataValue('authority_member'));
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
        '%person' => $this->getTempDataValue('authority_member'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(1), $this->getRouteParams());
  }

}
