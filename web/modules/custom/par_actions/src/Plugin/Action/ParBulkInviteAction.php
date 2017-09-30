<?php

namespace Drupal\par_actions\Plugin\Action;

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

    // Do some processing..
    // ...
    drupal_set_message($entity->label());
    return sprintf('Example action (configuration: %s)', print_r($this->configuration, TRUE));
  }

  public function getDefaultMessage() {
    $message_body = <<<HEREDOC
Dear [par-invite:recipient-name],

Primary Authority has been simplified and is now open to all UK businesses.

Simplifying the scheme has required the creation of an entirely new Primary Authority Register in order to accommodate the greater volume of businesses and partnerships.

In order to access the new PA Register, please click on the following link: [site:login-url]

After registering, you can continue to access the new PA Register at using the following link: [site:url]

Thanks for your help.
[par-invite:sender-name]
HEREDOC;

    return $message_body;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPreConfigurationForm(array $form, array $values, FormStateInterface $form_state) {
    $form['par_bulk_invite_message_body'] = [
      '#title' => $this->t('Message body'),
      '#type' => 'textarea',
      '#default_value' => isset($values['par_bulk_invite_message_body']) ? $values['par_bulk_invite_message_body'] : $this->getDefaultMessage(),
    ];
    $form['par_bulk_invite_message_subject'] = [
      '#title' => $this->t('Message subject'),
      '#type' => 'textfield',
      '#default_value' => isset($values['par_bulk_invite_message_subject']) ? $values['par_bulk_invite_message_subject'] : $this->getDefaultMessage(),
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
    $form['par_bulk_invite_message_body'] = [
      '#title' => $this->t('Message body'),
      '#type' => 'textarea',
      '#default_value' => $form_state->getValue('par_bulk_invite_message_body'),
    ];
    $form['par_bulk_invite_message_subject'] = [
      '#title' => $this->t('Message subject'),
      '#type' => 'textfield',
      '#default_value' => $form_state->getValue('par_bulk_invite_message_subject'),
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
    $this->configuration['example_config_setting'] = $form_state->getValue('example_config_setting');
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
