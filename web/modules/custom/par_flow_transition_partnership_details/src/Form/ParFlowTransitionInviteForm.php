<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;

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

  public function getFormId() {
    return 'par_flow_transition_partnership_invite_business';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param ParDataPartnership $par_data_partnership
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
      // Contact.
      $this->loadDataValue("email_subject", 'Important updates to the Primary Authority Register');
      $this->loadDataValue("email_body", $this->getMessageBody($par_data_person));
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
      '#type' => 'fieldset',
      '#title' => t('Your email'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'authority_email' => [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#hidden' => TRUE,
      ],
      'authority_email_display' => [
        '#type' => 'markup',
        '#markup' => t('par_authority@example.com'),
      ],
    ];

    // Get Recipient.
    $form['business_member'] = [
      '#type' => 'fieldset',
      '#title' => t('Business contact email'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'business_email' => [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#hidden' => TRUE,
      ],
      'business_email_display' => [
        '#type' => 'markup',
        '#markup' => t('par_business@example.com'),
      ],
    ];

    // Allow the message subject to be changed.
    $form['email_subject'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Message subject'),
      '#default_value' => $this->getDefaultValues('email_subject'),
    );

    // Allow the message body to be changed.
    $form['email_body'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Message'),
      '#default_value' => $this->getDefaultValues('email_body'),
    );

    $form['send'] = array(
      '#type' => 'submit',
      '#value' => t('Send Invitation'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $invite_type = $form_state->getBuildInfo()['args'][0];
    $invite = Invite::create(array('type' => $invite_type));
    $invite->field_invite_email_address->value = $form_state->getValue('email');
    $subject = $form_state->getValue('email_subject');
    if (!empty($subject)) {
      $invite->field_invite_email_subject->value = $subject;
    }
    $invite->setPlugin('invite_by_email');
    $invite->save();
  }

}
