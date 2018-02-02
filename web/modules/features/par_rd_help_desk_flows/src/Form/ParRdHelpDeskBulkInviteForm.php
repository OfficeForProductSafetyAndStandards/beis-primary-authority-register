<?php

namespace Drupal\par_rd_help_desk_flows\Form;

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

  /**
   * {@inheritdoc}
   */
  protected $flow = 'invite';

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
    if ($par_data_person) {
      // Set the default subject for the invite email, this can be changed by the user.
      $this->getFlowDataHandler()->setFormPermValue("email_subject", 'New Partnership on the Primary Authority Register');

      // Get the email for the business contact that this email will go to.
      $this->getFlowDataHandler()->setFormPermValue("business_member", $par_data_person->get('email')->getString());

      // Get the authority user's email and name.
      $account = User::load($this->currentUser()->id());
      $authority = current($par_data_partnership->get('field_authority')->referencedEntities());
      $authority_person = $this->getParDataManager()->getUserPerson($account, $authority);

      $this->getFlowDataHandler()->setFormPermValue("authority_member", $authority_person->get('email')->getString());
      $authority_person_name = $authority_person->getFullName();

      // Get the user accounts related to the business user.
      if ($business_user = $par_data_person->getUserAccount()) {
        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

A new partnership has been created for you by {$authority->get('authority_name')->getString()}. Please log in to the Primary Authority Register to update your business's details. To do this, please follow this link:

[site:login-url]

Thanks for your help.
{$authority_person_name}
HEREDOC;
      }
      else {
        $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

A new partnership has been created for you by {$authority->get('authority_name')->getString()}. Please create your account with the Primary Authority Register so that you can manage your business's details. To do this, please follow this link:

[invite:invite-accept-link]

Thanks for your help.
{$authority_person_name}
HEREDOC;
      }

      $this->getFlowDataHandler()->setFormPermValue("email_body", $message_body);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_person);

    $invite_type = $this->config('invite.invite_type.invite_organisation_member');
    $data = unserialize($invite_type->get('data'));

    // Get Sender.
    $form['authority_member'] = [
      '#type' => 'textfield',
      '#title' => t('Your email'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => $this->getCurrentUser()->getEmail(),
      '#description' => 'You cannot change your email here. If you want to send this invite from a different email address please contact the helpdesk.',
    ];
    $form['inviter'] = [
      '#type' => 'hidden',
      '#title' => t('Inviter'),
      '#value' => $this->getCurrentUser()->id(),
    ];

    // Get Recipient.
    $form['business_member'] = [
      '#type' => 'textfield',
      '#title' => t('Business contact email'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('business_member'),
      '#description' => 'This is the businesses primary contact. If you need to send this invite to another person please contact the helpdesk.',
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

    if (empty($form_state->getValue('email_subject'))) {
      $form_state->setErrorByName('email_subject', $this->t('<a href="#edit-email-subject">The Message subject is required.</a>'));
    }

    if (empty($form_state->getValue('email_body'))) {
      $form_state->setErrorByName('email_body', $this->t('<a href="#edit-email-body">The Message is required.</a>'));
    }
    // Check that the email body contains an invite accept link.
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    if ($business_user = $par_data_person->getUserAccount()) {
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
      'user_id' => $this->getFlowDataHandler()->getTempDataValue('inviter'),
      'invitee' => $this->getFlowDataHandler()->getTempDataValue('business_member'),
    ]);
    $invite->set('field_invite_email_address', $this->getFlowDataHandler()->getTempDataValue('business_member'));
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
        '%person' => $this->getFlowDataHandler()->getTempDataValue('business_member'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
