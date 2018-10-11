<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataEntityInterface;
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
class ParInviteForm extends ParBaseForm {

  /** @var invite type */
  protected $invite_type = 'invite_organisation_member';

  protected $pageTitle = 'Invite the business';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    // The invitation type must be set first.
    $this->getFlowDataHandler()->setFormPermValue('invitation_type', $this->invite_type);

    parent::loadData();

    $contact = $this->getFlowDataHandler()->getParameter('par_data_person');
    $sender_name = $this->getFlowDataHandler()->getDefaultValues('inviter_name', '');
    $inviting_authority = $this->getFlowDataHandler()->getDefaultValues('inviter_authority', '');

    $this->getFlowDataHandler()->setFormPermValue('subject', 'Invitation to complete your Primary Authority partnership application');
    $body = <<<HEREDOC
Dear {$contact->getFullName()},

A partnership application has been started for you with {$inviting_authority}. To complete it, please log on to the Primary Authority Register using the following link:

[invite:invite-accept-link]

It will be helpful to have basic details on your business to hand, including an overview of its activities; its trading names, registration numbers and SIC codes; and the number of employees. You will also need to confirm the main contact for partnership-related matters.

Thanks for your help.
{$sender_name}
HEREDOC;
    $this->getFlowDataHandler()->setFormPermValue('body', $body);

    // Change the primary action title.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Send invite');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $invitation_type = $this->getFlowDataHandler()->getDefaultValues('invitation_type', FALSE);

    // Override invite type if there were multiple roles to choose from.
    $roles = $this->getFlowDataHandler()->getDefaultValues('roles');
    $target_role = $this->getFlowDataHandler()->getDefaultValues('target_role');
    if ($roles && count($roles) > 1) {
      switch ($target_role) {
        case 'par_enforcement':
          $invitation_type = 'invite_enforcement_officer';

          break;
        case 'par_authority':
          $invitation_type = 'invite_authority_member';

          break;
        case 'par_organisation':
          $invitation_type = 'invite_organisation_member';

          break;
      }
    }

    $invite = Invite::create([
      'type' => $invitation_type,
      'user_id' => $this->getFlowDataHandler()->getTempDataValue('user_id'),
      'invitee' => $this->getFlowDataHandler()->getTempDataValue('to'),
    ]);
    $invite->set('field_invite_email_address', $this->getFlowDataHandler()->getTempDataValue('to'));
    $invite->set('field_invite_email_subject', $this->getFlowDataHandler()->getTempDataValue('subject'));
    $invite->set('field_invite_email_body', $this->getFlowDataHandler()->getTempDataValue('body'));
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
