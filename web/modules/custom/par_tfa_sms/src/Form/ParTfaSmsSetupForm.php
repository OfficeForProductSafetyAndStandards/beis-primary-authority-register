<?php

namespace Drupal\par_tfa_sms\Form;

use Drupal\encrypt\Exception\EncryptException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_tfa_sms\ParTfaSmsSetup;
use Drupal\user\UserStorageInterface;
use Drupal\user\UserDataInterface;
use Drupal\tfa\Form\TfaSetupForm;
use Drupal\tfa\TfaUserDataTrait;
use Drupal\tfa\TfaPluginManager;
use Drupal\user\Entity\User;

/**
 * TFA setup form router.
 */
class ParTfaSmsSetupForm extends TfaSetupForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_tfa_sms_setup';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, User $user = NULL, $method = 'par_tfa_sms', $reset = 0) {
    /** @var \Drupal\user\Entity\User $account */
    $account = $this->userStorage->load($this->currentUser()->id());

    $form['account'] = [
      '#type' => 'value',
      '#value' => $user,
    ];

    $storage = $form_state->getStorage();
    // Always require a password on the first time through.
    if (empty($storage)) {
      // Allow administrators to change TFA settings for another account.
      if ($account->id() == $user->id() && $account->hasPermission('administer tfa for other users')) {
        $current_pass_description = $this->t('Enter your current password to
        alter TFA settings for account %name.', ['%name' => $user->getAccountName()]);
      }
      else {
        $current_pass_description = $this->t('Enter your current password to continue.');
      }

      $form['current_pass'] = [
        '#type' => 'password',
        '#title' => $this->t('Current password'),
        '#size' => 25,
        '#required' => TRUE,
        '#description' => $current_pass_description,
        '#attributes' => ['autocomplete' => 'off'],
      ];

      $form['actions'] = ['#type' => 'actions'];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Confirm'),
      ];

      $form['actions']['cancel'] = [
        '#type' => 'submit',
        '#value' => $this->t('Cancel'),
        '#limit_validation_errors' => [],
        '#submit' => ['::cancelForm'],
      ];
    }
    else {
      // Record methods progressed.
      $plugin = $this->tfaPluginManager->getDefinition($method, FALSE);
      $setup_plugin = $this->tfaPluginManager->createInstance($plugin['id'], ['uid' => $user->id()]);
      $par_tfa_sms_setup = new ParTfaSmsSetup($setup_plugin);
      $form = $par_tfa_sms_setup->getForm($form, $form_state, $reset);
      $storage[$method] = $par_tfa_sms_setup;

      $form['actions']['#type'] = 'actions';
      $form['actions']['cancel'] = [
        '#type' => 'submit',
        '#value' => $this->t('Cancel'),
        '#limit_validation_errors' => [],
        '#submit' => ['::cancelForm'],
      ];
      // Record the method in progress regardless of whether in full setup.
      $storage['step_method'] = $method;

      // Validate the mobile number.
      if (isset($storage['mobile_provided'])) {
        /** @var \Drupal\par_tfa_sms\Plugin\Tfa\ParTfaSms $plugin */
        $plugin = \Drupal::service('plugin.manager.tfa')->createInstance(
          'par_tfa_sms',
          ['uid' => $this->currentUser()->id()]
        );
        $form = $plugin->getForm($form, $form_state);
      }
    }
    $form_state->setStorage($storage);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\user\Entity\User $user */
    $user = $this->userStorage->load($this->currentUser()->id());
    $storage = $form_state->getStorage();
    $values = $form_state->getValues();
    $account = $form['account']['#value'];
    if (isset($values['current_pass'])) {
      // Allow administrators to change TFA settings for another account using
      // their own password.
      if ($account->id() != $user->id()) {
        if ($user->hasPermission('administer tfa for other users')) {
          $account = $user;
        }
        // If current user lacks admin permissions, kick them out.
        else {
          throw new NotFoundHttpException();
        }
      }
      $current_pass = $this->passwordChecker->check(trim($form_state->getValue('current_pass')), $account->getPassword());
      if (!$current_pass) {
        $form_state->setErrorByName('current_pass', $this->t("Incorrect password."));
      }
      return;
    }
    elseif (!empty($storage['step_method'])) {
      $method = $storage['step_method'];
      $par_tfa_sms_setup = $storage[$method];
      // Validate plugin form.
      if (!$par_tfa_sms_setup->validateForm($form, $form_state)) {
        $messages = $par_tfa_sms_setup->getErrorMessages();
        if (!empty($messages)) {
          foreach ($messages as $element => $message) {
            $form_state->setErrorByName($element, $message);
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $account = $form['account']['#value'];
    $storage = $form_state->getStorage();
    $values = $form_state->getValues();

    // Password validation.
    if (isset($values['current_pass'])) {
      $storage['pass_confirmed'] = TRUE;
      $form_state->setRebuild();
      $form_state->setStorage($storage);
      return;
    }
    elseif (!empty($storage['code_verified'])) {
      $method = $storage['step_method'];
      $skipped_method = FALSE;

      if (!empty($storage[$method])) {
        // Trigger multi-step if in full setup.
        if (!empty($storage['full_setup'])) {
          $this->parTfaSmsNextSetupStep($form_state, $method, $storage[$method], $skipped_method);
        }

        // Plugin form submit.
        $setup_class = $storage[$method];
        if (!$setup_class->submitForm($form, $form_state)) {
          $this->messenger()->addError($this->t('There was an error during TFA setup. Your settings have not been saved.'));
          $form_state->setRedirect('tfa.overview', ['user' => $account->id()]);
          return;
        }
      }

      // Setup complete and return to overview page.
      $this->messenger()->addStatus($this->t('SMS setup complete.'));
      $form_state->setRedirect('tfa.overview', ['user' => $account->id()]);

      // Log and notify if this was full setup.
      if (!empty($storage['step_method'])) {
        $data = [
          'plugins' => $storage['step_method'],
          'sms' => TRUE,
        ];
        $this->tfaSaveTfaData($account->id(), $data);
        $this->logger('tfa')->info('SMS enabled for user @name UID @uid', [
          '@name' => $account->getAccountName(),
          '@uid' => $account->id(),
        ]);

        $params = ['account' => $account];
        $this->mailManager->mail('tfa', 'tfa_enabled_configuration', $account->getEmail(), $account->getPreferredLangcode(), $params);
      }
    }
    elseif (!empty($values['sms_phone_number'])) {
      $user = $this->userStorage->load($account->uid->value);
      $phone_number_stored = $user->get('phone_number')->value;

      if (empty($phone_number_stored)) {
        $user->set('phone_number', $form_state->getValue('sms_phone_number'));
        $user->save();
      }

      $storage['mobile_provided'] = TRUE;
      $form_state->setRebuild();
      $form_state->setStorage($storage);
      return;
    }
  }

  /**
   * Form cancel handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function cancelForm(array &$form, FormStateInterface $form_state) {
    $account = $form['account']['#value'];
    $this->messenger()->addWarning($this->t('SMS setup canceled.'));
    $form_state->setRedirect('tfa.overview', ['user' => $account->id()]);
  }

  /**
   * Set form rebuild, next step, and message if any plugin steps left.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param string $this_step
   *   The current setup step.
   * @param \Drupal\par_tfa_sms\ParTfaSmsSetup $step_class
   *   The setup instance of the current step.
   * @param bool $skipped_step
   *   Whether the step was skipped.
   */
  protected function parTfaSmsNextSetupStep(FormStateInterface &$form_state, $this_step, ParTfaSmsSetup $step_class, $skipped_step = FALSE) {
    $storage = $form_state->getStorage();
    // Remove this step from steps left.
    $storage['steps_left'] = array_diff($storage['steps_left'], [$this_step]);
    if (!empty($storage['steps_left'])) {
      // Contextual reporting.
      if ($output = $step_class->getSetupMessages()) {
        $output = $skipped_step ? $output['skipped'] : $output['saved'];
      }
      $count = count($storage['steps_left']);
      $output .= ' ' . $this->formatPlural($count, 'One setup step remaining.', '@count SMS setup steps remain.', ['@count' => $count]);
      if ($output) {
        $this->messenger()->addStatus($output);
      }

      // Set next step and mark form for rebuild.
      $next_step = array_shift($storage['steps_left']);
      $storage['step_method'] = $next_step;
      $form_state->setRebuild();
    }
    $form_state->setStorage($storage);
  }

}
