<?php

namespace Drupal\par_invite\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\login_destination\Entity\LoginDestination;
use Drupal\Core\Url;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParInviteRegistrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_invite_welcome';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,  $invite = NULL) {
    // The invite needs to be set and valid. If not then we need to go to an error page.
    if (!isset($invite)) {
      $form['intro'] = [
        '#markup' => t('<p>We are sorry but the token provided is no longer valid.</p>'),
      ];
      return $form;
    }

    $invite = \Drupal::routeMatch()->getParameter('invite');
    $invite_email = $invite->get('field_invite_email_address')->getString();

    // Need to check to see if the user has an account already.
    if (user_load_by_mail($invite_email)) {
      return $this->redirect('user.login');

    }
    $form["#tree"] = FALSE;
    $form['#form_id'] = $this->getFormId();

    // Account information.
    $form['account'] = [
      '#type'   => 'container',
      '#weight' => -10,
    ];

    $form['account']['intro'] = [
      '#markup' => t('<p>You have been invited to complete an account with the Primary Authority Register.<br><br>Please review the terms and conditions and complete your user account details below to be granted access to the register.</p>'),
    ];

    // Change password.
    $form['account']['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#default_value' => $invite_email,
      '#disabled' => TRUE,
    ];

    $form['account']['pass'] = [
      '#type' => 'password_confirm',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    // Make sure the email matches the one it has been sent to.
    $invite = \Drupal::routeMatch()->getParameter('invite');

    $invite_email = $invite->get('field_invite_email_address')->getString();
    if (empty($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t('<a href="#edit-email">The @field is required.</a>', ['@field' => $form['account']['email']['#title']]));
    }
    elseif ($form_state->getValue('email') != $invite_email) {
      $this->setErrorByName('email', $this->t('<a href="#edit-email">Email provided doesn\'t match the one the invite was sent to</a>'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Add the user to drupal.
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $user = User::create();

    // Mandatory.
    $user->setPassword($form_state->getValue('pass'));
    $user->enforceIsNew();
    $user->setEmail($form_state->getValue('email'));
    $user->setUsername($form_state->getValue('email'));

    // Optional.
    $user->set('init', 'email');
    $user->set('langcode', $language);
    $user->set('preferred_langcode', $language);
    $user->set('preferred_admin_langcode', $language);
    $user->activate();

    // Save user account.
    $result = $user->save();

    // If the account has been saved then need to redirect to the correct page.
    if ($result) {
      // Log the user into the site.
      user_login_finalize($user);
      $login_destination_manager = \Drupal::service('login_destination.manager');
      $path = $login_destination_manager->findDestination(LoginDestination::TRIGGER_REGISTRATION, $user);
      $url = Url::fromUri($path->destination_path);
      $form_state->setRedirectUrl( $url );
    }
    // What do we do if the user cannot be created?
  }

}
