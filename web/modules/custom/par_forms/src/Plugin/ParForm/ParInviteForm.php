<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormException;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Invite new users form plugin.
 *
 * @ParForm(
 *   id = "invite",
 *   title = @Translation("Invite new users form.")
 * )
 */
class ParInviteForm extends ParFormPluginBase {

  /**
   * Invitation messages
   */
  public function getMessage($invitation_type) {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $sender_name = $this->getFlowDataHandler()->getDefaultValues('inviter_name', FALSE);

    switch ($invitation_type) {
      default:
        $subject = 'Invitation to join the Primary Authority Register';
        $body = <<<HEREDOC
Dear {$par_data_person->getFullName()},

You are being invited to join the Primary Authority Register. Please create your account so that you can manage your partnerships. To do this, please follow this link:

[invite:invite-accept-link]

Thanks for your help.
{$sender_name}
HEREDOC;

    }

    return [
      'body' => $body,
      'subject' => $subject,
    ];
  }

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $invitation_type = $this->getFlowDataHandler()->getDefaultValues('invitation_type', FALSE);
    if ($invitation_type && $invite_type_config = $this->config("invite.invite_type.{$invitation_type}")) {
      $data = unserialize($invite_type_config->get('data'));
      $this->getFlowDataHandler()->setFormPermValue('invitation_type_data', $data);
    }

    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    if ($par_data_person && $par_data_person instanceof ParDataEntityInterface) {
      // Set the default recipient address.
      if (!$this->setDefaultValuesByKey("to", $cardinality)) {
        $this->getFlowDataHandler()->setTempDataValue('to', $par_data_person->getEmail());
      }

      // If the user already has an account then don't invite them.
      if ($account = $par_data_person->lookupUserAccount()) {
        $this->getFlowDataHandler()->setTempDataValue('existing', TRUE);
      }
    }

    // Set the default value for the sender
    if ($account = $this->getFlowNegotiator()->getCurrentUser()) {
      $this->getFlowDataHandler()->setTempDataValue("from", $account->getEmail());
      $this->getFlowDataHandler()->setTempDataValue("inviter", $account->id());

      // Identify the sender's name, for use in the message template.
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');
      $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
      if ($par_data_partnership && !$par_data_authority) {
        $par_data_authority = $par_data_partnership->getAuthority(TRUE);
        $this->getFlowDataHandler()->setTempDataValue("inviter_authority", $par_data_authority->label());
      }
      if ($par_data_partnership && !$par_data_organisation) {
        $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
      }
      $authority_person = $par_data_authority ? $this->getParDataManager()->getUserPerson($account, $par_data_authority) : NULL;
      $organisation_person = $par_data_organisation ? $this->getParDataManager()->getUserPerson($account, $par_data_organisation) : NULL;
      if($account->hasPermission('invite authority members')) {
        $sender_name = 'BEIS RD Department';
      }
      elseif ($authority_person) {
        $sender_name = $authority_person->getFullName();
      }
      elseif ($organisation_person) {
        $sender_name = $organisation_person->getFullName();
      }
      else {
        $sender_name = '';
      }
      $this->getFlowDataHandler()->setFormPermValue("inviter_name", $sender_name);
    }

    // Get message subject and body if not already set.
    if ($message = $this->getMessage(NULL)) {
      if (!$this->getFlowDataHandler()->getDefaultValues("subject", FALSE)) {
        $this->getFlowDataHandler()->setFormPermValue("subject", $message['subject']);
      }
      if (!$this->getFlowDataHandler()->getDefaultValues("body", FALSE)) {
        $this->getFlowDataHandler()->setFormPermValue("body", $message['body']);
      }
    }

    // Determine which roles can be invited.
    foreach (user_roles() as $user_role) {
      if (empty($user_role->get('_core'))) {
        $par_roles[$user_role->id()] = $user_role->label();
      }
    }
    if (!empty($par_roles)) {
      $roles = array_filter($par_roles, function ($role_id) use($invitation_type) {
        if ($invitation_type === 'invite_authority_member') {
          return in_array($role_id, ['par_authority', 'par_enforcement']);
        }
        if ($invitation_type === 'invite_organisation_member') {
          return in_array($role_id, ['par_organisation']);
        }
        return FALSE;
      }, ARRAY_FILTER_USE_KEY);
      if (!$this->getFlowDataHandler()->getDefaultValues("roles", FALSE)) {
        $this->getFlowDataHandler()->setFormPermValue("roles", $roles);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // There must be an invitation type specified.
    if (!$this->getFlowDataHandler()->getDefaultValues('invitation_type', FALSE)) {
      throw new ParFormException('There is no invitation type selected for this invitation.');
    }

    // If the contact has an existing user account skip the invitation.
    if ($this->getFlowDataHandler()->getDefaultValues('existing', FALSE)) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    // There must be a sender and a recipient to continue.
    if (!$this->getDefaultValuesByKey('from', $cardinality, FALSE) || !$this->getDefaultValuesByKey('to', $cardinality, FALSE)) {
      $form['no_recipient'] = [
        '#type' => 'fieldset',
        '#title' => t('No recipient'),
      ];
      $form['no_recipient']['message'] = [
        '#markup' => '<p>We can\'t continue sending this invitation because no recipient has been selected, please return to the previous page and try again.</p>',
      ];

      // Don't allow to continue.
      $this->getFlowNegotiator()->getFlow()->disableAction('save');
      $this->getFlowNegotiator()->getFlow()->disableAction('next');

      return $form;
    }

    // Set the sender values and display the email.
    $form['sender'] = [
      '#type' => 'fieldset',
      '#title' => t('Sender\'s email address'),
      [
        '#markup' => "<p>{$this->getFlowDataHandler()->getDefaultValues('from')}</p>"
      ],
      'from' => [
        '#type' => 'hidden',
        '#value' => $this->getFlowDataHandler()->getDefaultValues('from'),
      ],
      'user_id' => [
        '#type' => 'hidden',
        '#value' => $this->getFlowDataHandler()->getDefaultValues('inviter'),
      ],
    ];

    // Set the recipient values and display the email.
    $form['recipient'] = [
      '#type' => 'fieldset',
      '#title' => t('Recipient\'s email address'),
      [
        '#markup' => "<p>{$this->getFlowDataHandler()->getDefaultValues('to')}</p>"
      ],
      'to' => [
        '#type' => 'hidden',
        '#value' => $this->getFlowDataHandler()->getDefaultValues('to'),
      ],
    ];

    $form['email'] = [
      '#type' => 'fieldset',
      '#title' => t('Enter your message'),
      '#description' => 'When you click ‘Continue’ below, your partner business will be emailed this invitation to log on to the Primary Authority Register and provide the information required to complete this application. You can amend the message if you wish but please do not change or delete the acceptance link.<br><br>'
    ];

    // Allow the message subject to be changed.
    $form['email']['subject'] = [
      '#type' => 'textfield',
      '#title' => t('Message subject'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('subject'),
    ];

    // Allow the message body to be changed.
    $form['email']['body'] = [
      '#type' => 'textarea',
      '#rows' => 18,
      '#title' => t('Message'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('body'),
    ];

    // Allow the invited role to be changed or set it if no choice is available.
    $roles = $this->getFlowDataHandler()->getDefaultValues('roles');
    if ($roles && count($roles) > 1) {
      $form['target_role'] = [
        '#type' => 'select',
        '#required' => TRUE,
        '#title' => t('Role'),
        '#description' => t('Choose which role to give this person.'),
        // @todo reduce options.
        '#options' => $roles,
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('default_role'),
      ];
    }
    elseif ($roles && count($roles) === 1) {
      $form['target_role'] = [
        '#type' => 'hidden',
        '#value' => key($roles),
      ];
    }

    return $form;
  }

  /**
   * Validate date field.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $data_policy_key = $this->getElementKey('target_role');
    if (!$form_state->getValue($data_policy_key)) {
      $id_key = $this->getElementKey('data_policy', $cardinality, TRUE);
      $message = $this->wrapErrorMessage('No role has been selected for this invitation.', $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($data_policy_key), $message);
    }

    $email_subject_key = $this->getElementKey('subject');
    if (empty($form_state->getValue($email_subject_key))) {
      $id_key = $this->getElementKey(['email', 'subject'], $cardinality, TRUE);
      $message = $this->wrapErrorMessage("Please enter a subject.", $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($email_subject_key), $message);
    }

    $email_body_key = $this->getElementKey('body');
    if (empty($form_state->getValue($email_body_key))) {
      $id_key = $this->getElementKey(['email', 'body'], $cardinality, TRUE);
      $message = $this->wrapErrorMessage("Please enter a message.", $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($email_body_key), $message);
    }

    if (!strpos($form_state->getValue($email_body_key), '[invite:invite-accept-link]')) {
      $id_key = $this->getElementKey(['email', 'body'], $cardinality, TRUE);
      $message = $this->wrapErrorMessage("Please make sure you have the invite link '[invite:invite-accept-link]' somewhere in your message.", $this->getElementId($id_key, $form));
      $form_state->setErrorByName($this->getElementName($email_body_key), $message);
    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
