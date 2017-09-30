<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsPreconfigurationInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * An example action covering most of the possible options.
 *
 * If type is left empty, action will be selectable for all
 * entity types.
 *
 * @Action(
 *   id = "par_bulk_invite",
 *   label = @Translation("Bulk invite PAR users."),
 *   type = "",
 *   confirm = TRUE,
 *   pass_context = TRUE,
 *   pass_view = TRUE
 * )
 */
class ParBulkInviteAction extends ViewsBulkOperationsActionBase implements ViewsBulkOperationsPreconfigurationInterface {

  /**
   * Getter for the Par Data Manager service.
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /*
     * All config resides in $this->configuration.
     * Passed view rows will be available in $this->context.
     * Data about the view used to select results and optionally
     * the batch context are available in $this->context or externally
     * through the public getContext() method.
     * The entire ViewExecutable object  with selected result
     * rows is available in $this->view or externally through
     * the public getView() method.
     */

    if ($entity instanceof ParDataPartnership) {
      $all_members = (bool) $this->configuration['par_bulk_invite_all_authority_members'];

      // If we're inviting all members get the authority straight from the partnership.
      if ($all_members === TRUE) {
        $par_data_authority = current($entity->get('field_authority')->referencedEntities());
        $partnership_members = $par_data_authority->get('field_person')->referencedEntities();
      }
      else {
        // Get the authority from the partnership and send to all the members.
        $partnership_members = $entity->get('field_authority_person')->referencedEntities();

        // Make sure they are members of the authority.
        $par_data_authority = current($entity->get('field_authority')->referencedEntities());
        $authority_members = $par_data_authority->retrieveEntityIds('field_person');
      }

      $token_service = \Drupal::token();

      foreach ($partnership_members as $delta => $member) {
        drupal_set_message($member->id());
        // Don't invite the user if they already have an account.
        $account_exists = (bool) $this->getParDataManager()->getEntitiesByProperty('user', 'mail', $member->get('email')->getString());

        if (in_array($member->id(), $authority_members) && !$account_exists) {
          try {
            $subject = $token_service->replace($this->configuration['par_bulk_invite_message_subject'], ['par' => $member]);
            $body = $token_service->replace($this->configuration['par_bulk_invite_message_body'], ['par' => $member]);

            $invite = Invite::create([
              'type' => 'invite_authority_member',
              'user_id' => \Drupal::currentUser()->id(),
              'invitee' => \Drupal::currentUser()->getEmail(),
            ]);
            $invite->set('field_invite_email_address', $member->get('email')->getString());
            $invite->set('field_invite_email_subject', $subject);
            $invite->set('field_invite_email_body', $body);
            $invite->setPlugin('invite_by_email');

            $invite->save();
          }
          catch (\Exception $e) {
            drupal_set_message("Error occurred executing invitation: {$e->getMessage()}", 'error');
          }
        }
      }
    }
  }

  /**
   * Getter for the default invitation message.
   *
   * @return string
   */
  public function getDefaultMessage() {
    $message_body = <<<HEREDOC
Dear [par:member-name],

Primary Authority has been simplified and is now open to all UK businesses.

Simplifying the scheme has required the creation of an entirely new Primary Authority Register in order to accommodate the greater volume of businesses and partnerships.

In order to access the new PA Register, please click on the following link: [site:login-url]

After registering, you can continue to access the new PA Register at using the following link: [site:url]

Thanks for your help.
HEREDOC;

    return $message_body;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPreConfigurationForm(array $form, array $values, FormStateInterface $form_state) {
    $form['par_bulk_invite_all_authority_members'] = [
      '#title' => $this->t('Invite all authority members'),
      '#description' => $this->t('If checked this will send invites to all members of the authority, not just those responsible for a partnership.'),
      '#type' => 'checkbox',
      '#default_value' => isset($values['par_bulk_invite_all_authority_members']) ? TRUE : FALSE,
    ];
    $form['par_bulk_invite_message_body'] = [
      '#title' => $this->t('Message body'),
      '#type' => 'textarea',
      '#default_value' => isset($values['par_bulk_invite_message_body']) ? $values['par_bulk_invite_message_body'] : $this->getDefaultMessage(),
    ];
    $form['par_bulk_invite_message_subject'] = [
      '#title' => $this->t('Message subject'),
      '#type' => 'textfield',
      '#default_value' => isset($values['par_bulk_invite_message_subject']) ? $values['par_bulk_invite_message_subject'] : 'New Primary Authority Register',
    ];

    return $form;
  }

  /**
   * Configuration form builder.
   *
   * If this method has implementation, the action is
   * considered to be configurable.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return array
   *   The configuration form.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $body = $form_state->getValue('par_bulk_invite_message_body') ?: $this->getDefaultMessage();
    $subject = $form_state->getValue('par_bulk_invite_message_subject') ?: 'New Primary Authority Register';
    $form['par_bulk_invite_all_authority_members'] = [
      '#title' => $this->t('Invite all authority members'),
      '#description' => $this->t('If checked this will send invites to all members of the authority, not just those responsible for a partnership.'),
      '#type' => 'checkbox',
      '#default_value' => $form_state->getValue('par_bulk_invite_message_subject') ? TRUE : FALSE,
    ];
    $form['par_bulk_invite_message_subject'] = [
      '#title' => $this->t('Message subject'),
      '#type' => 'textfield',
      '#default_value' => $subject,
    ];
    $form['par_bulk_invite_message_body'] = [
      '#title' => $this->t('Message body'),
      '#type' => 'textarea',
      '#default_value' => $body,
    ];

    return $form;
  }

  /**
   * Submit handler for the action configuration form.
   *
   * If not implemented, the cleaned form values will be
   * passed direclty to the action $configuration parameter.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // This is not required here, when this method is not defined,
    // form values are assigned to the action configuration by default.
    // This function is a must only when user input processing is needed.
    $this->configuration['par_bulk_invite_all_authority_members'] = $form_state->getValue('par_bulk_invite_all_authority_members');
    $this->configuration['par_bulk_invite_message_body'] = $form_state->getValue('par_bulk_invite_message_body');
    $this->configuration['par_bulk_invite_message_subject'] = $form_state->getValue('par_bulk_invite_message_subject');
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if (!$account->hasPermission('invite authority members')) {
      return FALSE;
    }

    // Other entity types may have different
    // access methods and properties.
    return TRUE;
  }

}
