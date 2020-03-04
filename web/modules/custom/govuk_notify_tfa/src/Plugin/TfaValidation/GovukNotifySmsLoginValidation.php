<?php

namespace Drupal\govuk_notify_tfa\Plugin\TfaValidation;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\govuk_notify\NotifyService\NotifyServiceInterface;
use Drupal\user\Entity\User;
use ParagonIE\ConstantTime\Encoding;
use Drupal\Core\Form\FormStateInterface;
use Drupal\encrypt\EncryptionProfileManagerInterface;
use Drupal\encrypt\EncryptServiceInterface;
use Drupal\tfa\Plugin\TfaBasePlugin;
use Drupal\tfa\Plugin\TfaValidationInterface;
use Drupal\user\UserDataInterface;
use Otp\Otp;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TOTP validation class for performing TOTP validation.
 *
 * @TfaValidation(
 *   id = "tfa_sms",
 *   label = @Translation("Notify SMS verification"),
 *   description = @Translation("GovUK Notify SMS validation Plugin"),
 *   fallbacks = {},
 *   isFallback = FALSE
 * )
 */
class GovukNotifySmsLoginValidation extends TfaBasePlugin implements TfaValidationInterface {
  use StringTranslationTrait;

  const USER_SEED_KEY = 'tfa_sms_seed';
  const USER_PHONE_KEY = 'tfa_sms_phone_number';

  /**
   * External otp library.
   *
   * @var Otp
   */
  public $otp;

  /**
   * External GovUK Notify library.
   *
   * @var \Drupal\govuk_notify\NotifyService\GovUKNotifyService
   */
  public $notify;

  /**
   * The time-window in which the validation should be done.
   *
   * @var int
   */
  public $timeSkew;

  /**
   * Whether or not the prefix should use the site name.
   *
   * @var bool
   */
  protected $siteNamePrefix;

  /**
   * Name prefix.
   *
   * @var string
   */
  protected $namePrefix;

  /**
   * Configurable name of the issuer.
   *
   * @var string
   */
  protected $issuer;

  /**
   * Configurable template ID to use for sending SMS messages.
   *
   * @var string
   */
  protected $templateId;

  /**
   * Choose which field to use for storing the user phone number.
   *
   * This will default to UserData storage if not set.
   *
   * @var string
   */
  protected $phoneField;

  /**
   * Whether the code has already been used or not.
   *
   * @var bool
   */
  protected $alreadyAccepted;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UserDataInterface $user_data, EncryptionProfileManagerInterface $encryption_profile_manager, EncryptServiceInterface $encrypt_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $user_data, $encryption_profile_manager, $encrypt_service);
    $this->otp = new Otp();
    $this->notify = $this->getNotifyService();

    // Allow codes within tolerance range of 3 * 30 second units.
    $plugin_settings = \Drupal::config('tfa.settings')->get('validation_plugin_settings');
    $settings = isset($plugin_settings['tfa_sms']) ? $plugin_settings['tfa_sms'] : [];
    $settings = array_replace([
      'time_skew' => 20,
      'site_name_prefix' => TRUE,
      'name_prefix' => 'TFA',
      'issuer' => 'Drupal',
      'template_id' => 'tfa_sms',
      'phone_field' => '',
    ], $settings);
    $this->timeSkew = $settings['time_skew'];
    $this->siteNamePrefix = $settings['site_name_prefix'];
    $this->namePrefix = $settings['name_prefix'];
    $this->issuer = $settings['issuer'];
    $this->templateId = $settings['template_id'];
    $this->phoneField = $settings['phone_field'];
    $this->alreadyAccepted = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('user.data'),
      $container->get('encrypt.encryption_profile.manager'),
      $container->get('encryption')
    );
  }

  public function getNotifyService() {
    return \Drupal::service('govuk_notify.notify_service');
  }

  public function getEntityFieldManager() {
    return \Drupal::service('entity_field.manager');
  }

  public function getUser() {
    return User::load($this->uid);
  }

  /**
   * {@inheritdoc}
   */
  public function ready() {
    $seed = ($this->getSeed() !== FALSE);

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getForm(array $form, FormStateInterface $form_state) {
    $recovery_methods = [];
    if ($this->getUserData('tfa', 'tfa_recovery_code', $this->uid, $this->userData) && $this->getFallbacks()) {
      $recovery_methods[] = 'Use one of your recovery codes.';
    }
    $recovery_methods[] = 'Please contact the helpdesk if you need assistance on 0121 345 1201 or email pa@beis.gov.uk';

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => t('SMS verification code'),
      '#description' => t('Enter the @length digit code that was sent to you by SMS.', ['@length' => $this->codeLength]),
      '#required'  => TRUE,
      '#attributes' => ['autocomplete' => 'off'],
    ];

    $form['recovery'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => "Can't access your account?",
      '#items' => $recovery_methods,
      '#attributes' => ['class' => ['list', 'list-bullet']],
      '#wrapper_attributes' => ['class' => ['form-group']],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['resend'] = [
      '#type'  => 'submit',
      '#name' => 'send',
      '#value' => t('Re-send code'),
      '#limit_validation_errors' => [],
    ];
    $form['actions']['login'] = [
      '#type'  => 'submit',
      '#name' => 'verify',
      '#value' => t('Verify'),
    ];

    return $form;
  }

  public function buildConfigurationForm($config, $state) {
    $user_fields = [];
    foreach ($this->getEntityFieldManager()->getFieldDefinitions('user', 'user') as $field_name => $field_definition) {
      $user_fields[$field_name] = $field_definition->getLabel();
    }

    $settings_form['time_skew'] = [
      '#type' => 'textfield',
      '#title' => t('Time Skew'),
      '#default_value' => ($this->timeSkew) ?: 30,
      '#description' => 'Number of 30 second chunks to allow TOTP keys between. For example choosing 10 will give users 5 minutes to enter their codes.',
      '#size' => 2,
      '#states' => $state,
      '#required' => TRUE,
    ];

    $settings_form['site_name_prefix'] = [
      '#type' => 'checkbox',
      '#title' => t('Use site name as OTP code name prefix.'),
      '#default_value' => ($this->siteNamePrefix) ? FALSE : TRUE,
      '#description' => t('If checked, the site name will be used instead of a static string. This can be useful for multi-site installations.'),
      '#states' => $state,
    ];

    // hide custom name prefix when site name prefix is selected
    $state['visible'] += [
      ':input[name="validation_plugin_settings[tfa_sms][site_name_prefix]"]' => ['checked' => FALSE]
    ];

    $settings_form['name_prefix'] = [
      '#type' => 'textfield',
      '#title' => t('OTP Code Prefix'),
      '#default_value' => ($this->namePrefix) ?: 'tfa',
      '#description' => 'Prefix for OTP code names. Suffix is account username.',
      '#size' => 15,
      '#states' => $state,
    ];

    $settings_form['issuer'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Issuer'),
      '#default_value' => $this->issuer,
      '#description' => $this->t("The provider or service this account is associated with."),
      '#size' => 15,
      '#required' => TRUE,
    ];

    $settings_form['template_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Notify Template ID'),
      '#default_value' => $this->templateId,
      '#description' => $this->t("The Notify template ID to use for sending messages, this can be retrieved from your Notify account."),
      '#size' => 15,
      '#required' => TRUE,
    ];

    // @TODO Allow a field to be choosen, more validation on which field is required.
    $settings_form['phone_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Phone number field'),
      '#options' => $user_fields,
      '#disabled' => TRUE,
      '#default_value' => $this->phoneField,
      '#description' => $this->t("The user field that stores the phone number. By default this will be stored as user data."),
      '#size' => 15,
    ];

    return $settings_form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array $form, FormStateInterface $form_state) {
    // Do not validate if the code is being re-sent.
    if ($form_state->getTriggeringElement()['#name'] === 'send') {
      return TRUE;
    }

    $values = $form_state->getValues();
    if (!$this->validate($values['code'])) {
      $this->errorMessages['code'] = t('Invalid application code. Please try again.');
      if ($this->alreadyAccepted) {
        $form_state->clearErrors();
        $this->errorMessages['code'] = t('Invalid code, it was recently used for a login. Please try to re-send your code.');
      }
      return FALSE;
    }
    else {
      // Store accepted code to prevent replay attacks.
      $this->storeAcceptedCode($values['code']);
      return TRUE;
    }
  }

  /**
   * Simple validate for web services.
   *
   * @param int $code
   *   OTP Code.
   *
   * @return bool
   *   True if validation was successful otherwise false.
   */
  public function validateRequest($code) {
    if ($this->validate($code)) {
      $this->storeAcceptedCode($code);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function validate($code) {
    // Strip whitespace.
    $code = preg_replace('/\s+/', '', $code);
    if ($this->alreadyAcceptedCode($code)) {
      $this->isValid = FALSE;
    }
    else {
      // Get OTP seed.
      $seed = $this->getSeed();
      $this->isValid = ($seed && $this->otp->checkTotp($this->encodeSeed($seed), $code, $this->timeSkew));
    }
    return $this->isValid;
  }

  /*
   * Allows a code to be sent.
   */
  public function send() {
    $seed = $this->getSeed();
    $code = $seed ? $this->otp->totp($this->encodeSeed($seed)) : NULL;

    $phone = $this->getPhone();

    if ($code && $phone) {
      $phone = '';
      $params = ['code' => $code];

      $this->notify->sendSms($phone, $this->templateId, $params);
    }
  }

  /**
   * Returns whether code has already been used or not.
   *
   * @return bool
   *   True is code already used otherwise false.
   */
  public function isAlreadyAccepted() {
    return $this->alreadyAccepted;
  }

  /**
   * Encode the seed for use in otp library
   *
   * @return string
   *   The encoded safe string for otp use.
   */
  public function encodeSeed($seed) {
    return Encoding::base32DecodeUpper($seed);
  }

  /**
   * Get seed for this account.
   *
   * @return string
   *    Decrypted account OTP seed or FALSE if none exists.
   */
  public function getSeed() {
    // Lookup seed for account and decrypt.
    $result = $this->getUserData('tfa', self::USER_SEED_KEY, $this->uid, $this->userData);
    if (!empty($result)) {
      $encrypted = base64_decode($result['seed']);
      $seed = $this->decrypt($encrypted);
      if (!empty($seed)) {
        return $seed;
      }
    }
    return FALSE;
  }

  /**
   * Save seed for account.
   *
   * @param string $seed
   *   Un-encrypted seed.
   */
  public function storeSeed($seed) {
    // Encrypt seed for storage.
    $encrypted = $this->encrypt($seed);

    $record = [
      self::USER_SEED_KEY => [
        'seed' => base64_encode($encrypted),
        'created' => REQUEST_TIME,
      ],
    ];

    $this->setUserData('tfa', $record, $this->uid, $this->userData);
  }

  /**
   * Delete the seed of the current validated user.
   */
  protected function deleteSeed() {
    $this->deleteUserData('tfa', self::USER_SEED_KEY, $this->uid, $this->userData);
  }

  /**
   * Get Phone number.
   */
  public function getPhone() {
    $user_fields = $this->getEntityFieldManager()->getFieldDefinitions('user', 'user');

    $phone = !empty($this->phoneField) && isset($user_fields[$this->phoneField]) ?
      $this->getUser()->get($this->phoneField)->getString() :
      $this->getUserData('tfa', self::USER_PHONE_KEY, $this->uid, $this->userData);

    return !empty($phone) ? $phone : NULL;
  }

  /**
   * Get Phone number.
   */
  public function setPhone($phone_number) {
    $user_fields = $this->getEntityFieldManager()->getFieldDefinitions('user', 'user');

    if (!empty($this->phoneField) && isset($user_fields[$this->phoneField])) {
      $user = $this->getUser();
      $user->set($this->phoneField, $phone_number);
      $user->save();
    }
    else {
      $this->setUserData('tfa', [self::USER_PHONE_KEY => $phone_number], $this->uid, $this->userData);
    }
  }

  /**
   * Get Phone number.
   */
  public function deletePhone() {
    $user_fields = $this->getEntityFieldManager()->getFieldDefinitions('user', 'user');

    if (!empty($this->phoneField) && isset($user_fields[$this->phoneField])) {
      $user = $this->getUser();
      $user->set($this->phoneField, NULL);
      $user->save();
    }
    else {
      $this->deleteUserData('tfa', self::USER_PHONE_KEY, $this->uid, $this->userData);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbacks() {
    return ($this->pluginDefinition['fallbacks']) ?: '';
  }

  /**
   * {@inheritdoc}
   */
  public function isFallback() {
    return ($this->pluginDefinition['isFallback']) ?: FALSE;
  }

}
