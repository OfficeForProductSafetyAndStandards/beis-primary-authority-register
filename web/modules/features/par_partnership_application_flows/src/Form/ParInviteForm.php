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

    $cid_contact = $this->getFlowNegotiator()->getFormKey('contact');
    $recipient_name = $this->getFlowDataHandler()->getDefaultValues('first_name', '', $cid_contact);
    $recipient_email = $this->getFlowDataHandler()->getDefaultValues('email', '', $cid_contact);

    $this->getFlowDataHandler()->setTempDataValue('to', $recipient_email);
    $this->getFlowDataHandler()->setFormPermValue("recipient_name", $recipient_name);

    parent::loadData();

    // This must overwrite the plugin defaults.
    $sender_name = $this->getFlowDataHandler()->getDefaultValues('inviter_name', '');
    $this->getFlowDataHandler()->getDefaultValues('inviter_name', '');
    $inviting_authority = $this->getFlowDataHandler()->getDefaultValues('inviter_authority', '');

    $this->getFlowDataHandler()->setFormPermValue('subject', 'Invitation to complete your Primary Authority partnership application');
    $body = <<<HEREDOC
Dear {$recipient_name},

A partnership application has been started for you with {$inviting_authority}. To complete it, please log on to the Primary Authority Register using the following link:

[invite:invite-accept-link]

It will be helpful to have basic details on your business to hand, including an overview of its activities; its trading names, registration numbers and SIC codes; and the number of employees. You will also need to confirm the main contact for partnership-related matters.

Thanks for your help.
{$sender_name}
HEREDOC;

    $this->getFlowDataHandler()->setFormPermValue('body', $body);
  }

}
