<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\user\Entity\User;

/**
 * Class InviteByEmailBlockForm.
 *
 * @package Drupal\invite\Form
 */
class ParFlowTransitionInviteForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_partnership_details';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_partnership_invite_business';
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
      // Set the default subject for the invite email, this can be changed by the user.
      $this->loadDataValue("email_subject", 'Important updates to the Primary Authority Register');

      $account = User::load($this->currentUser()->id());
      $authority_person_name = '';
      foreach ($this->parDataManager->getUserPeople($account) as $authority_person) {
        if ($par_data_partnership->isAuthorityMember($authority_person)) {
          $authority_person_name = $authority_person->getFullName();
          break 1;
        }
      }
      $message_body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

I'm writing to ask you to check and update if necessary the information held about your business in the Primary Authority Register. To do this, please follow this link:

[invite:invite-accept-link]

The Department for Business, Energy and Industrial Strategy is making changes to the Primary Authority scheme. From October, a new version of the Primary Authority Register website will be launched. We're asking all businesses like yours to update their details in the Register so that all information in the new website is correct when it launches.

Thanks for your help.
{$authority_person_name}
HEREDOC;
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
    $form['business_member'] = [
      '#type' => 'textfield',
      '#title' => t('Business contact email'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => 'par_business@example.com',
      '#description' => 'This is the businesses primary contact and cannot be changed here. If you need to send this invite to another person please contact the helpdesk.',
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
      '#value' => t('Send Invitation'),
    ];

    $previous_link = $this->getFlow()->getLinkByStep(3)->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $previous_link]),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
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
      'type' => 'invite_organisation_member',
      'user_id' => $this->getTempDataValue('inviter'),
      'invitee' => $this->getTempDataValue('business_member'),
    ]);
    $invite->set('field_invite_email_address', $this->getTempDataValue('business_member'));
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
        '%person' => $this->getTempDataValue('business_member'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(3), $this->getRouteParams());
  }

}
