<?php

namespace Drupal\par_tfa_sms\Plugin\Tfa;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\govuk_notify\NotifyService\GovUKNotifyService;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\encrypt\EncryptionProfileManagerInterface;
use Drupal\encrypt\Exception\EncryptException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\tfa\Plugin\TfaValidationInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\encrypt\EncryptServiceInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\tfa\Plugin\TfaSetupInterface;
use Drupal\user\UserStorageInterface;
use Drupal\Core\Database\Connection;
use Drupal\user\UserDataInterface;
use Drupal\tfa\TfaBasePlugin;
use Drupal\user\Entity\User;
use Otp\GoogleAuthenticator;
use Drupal\Core\Url;
use Otp\Otp;

/**
 * Recovery validation class for performing recovery codes validation.
 *
 * @Tfa(
 *   id = "par_tfa_sms",
 *   label = @Translation("SMS Code"),
 *   description = @Translation("SMS Code Validation Plugin"),
 *   setupMessages = {
 *    "saved" = @Translation("SMS code verified."),
 *    "skipped" = @Translation("SMS code not enabled."),
 *   }
 * )
 */
class ParTfaSms extends TfaBasePlugin implements TfaValidationInterface, TfaSetupInterface, ContainerFactoryPluginInterface {

  /**
   * The Datetime service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The time-window in which the validation should be done.
   *
   * @var int
   */
  protected $timeSkew;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The GovUK Notify service.
   *
   * @var \Drupal\govuk_notify\NotifyService\GovUKNotifyService
   */
  protected $govUkNotifyService;

  /**
   * Un-encrypted code.
   *
   * @var string
   */
  protected $code;

  /**
   * Un-encrypted seed.
   *
   * @var string
   */
  protected $seed;

  /**
   * Encryption profile.
   *
   * @var \Drupal\encrypt\EncryptionProfileManagerInterface
   */
  protected $encryptionProfile;

  /**
   * Encryption service.
   *
   * @var \Drupal\encrypt\EncryptService
   */
  protected $encryptService;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection;
   */
  protected $dbConnection;

  /**
   * Constructs a new Tfa plugin object.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   The user storage.
   * @param \Drupal\govuk_notify\NotifyService\GovUKNotifyService $govuk_notify_service
   *   The GovUkNotify service.
   * @param \Drupal\encrypt\EncryptionProfileManagerInterface $encryption_profile_manager
   *   Encryption profile manager.
   * @param \Drupal\encrypt\EncryptServiceInterface $encrypt_service
   *   Encryption service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The datetime service.
   * @param \Drupal\user\UserDataInterface $user_data
   *   User data object to store user specific information.
   * @param \Drupal\Core\Database\Connection $db_connection
   *   The database connection.
   */
  public function __construct(
      array $configuration, $plugin_id, $plugin_definition,
      ConfigFactoryInterface $config_factory,
      UserStorageInterface $user_storage,
      GovUKNotifyService $govuk_notify_service,
      EncryptionProfileManagerInterface $encryption_profile_manager,
      EncryptServiceInterface $encrypt_service,
      TimeInterface $time,
      UserDataInterface $user_data,
      Connection $db_connection,
    ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->auth = new \StdClass();
    $this->auth->otp = new Otp();
    $this->auth->ga = new GoogleAuthenticator();
    $this->configFactory = $config_factory;

    // Allow codes within tolerance range of 2 * 30 second units.
    $plugin_settings = $config_factory->get('tfa.settings')->get('validation_plugin_settings');
    $settings = $plugin_settings[$plugin_id] ?? [];

    // Set time_skew to 10 to give codes a 5 minute expiry time.
    $settings = array_replace([
      'time_skew' => 10,
      'site_name_prefix' => TRUE,
      'name_prefix' => 'SMS',
      'issuer' => 'PAR',
    ], $settings);

    $this->userStorage = $user_storage;
    $this->govUkNotifyService = $govuk_notify_service;
    $this->encryptionProfile = $encryption_profile_manager->getEncryptionProfile($config_factory->get('tfa.settings')->get('encryption'));
    $this->encryptService = $encrypt_service;
    $this->time = $time;
    $this->timeSkew = $settings['time_skew'];
    $this->userData = $user_data;
    $this->dbConnection = $db_connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager')->getStorage('user'),
      $container->get('govuk_notify.notify_service'),
      $container->get('encrypt.encryption_profile.manager'),
      $container->get('encryption'),
      $container->get('datetime.time'),
      $container->get('user.data'),
      $container->get('database')
    );
  }

  /**
   * Create OTP seed for account.
   *
   * @return string
   *   Un-encrypted seed.
   */
  protected function createSeed() {
    return $this->auth->ga->generateRandom();
  }

  /**
   * Get seed for this account.
   *
   * @return string
   *   Decrypted account OTP seed or FALSE if none exists.
   */
  protected function getSeed() {
    // Lookup seed for account and decrypt.
    $result = $this->getUserData('tfa', 'par_tfa_sms_seed', $this->uid);

    if (!empty($result)) {
      $encrypted = base64_decode($result['seed']);
      $seed = $this->encryptService->decrypt($encrypted, $this->encryptionProfile);
      if (!empty($seed)) {
        return $seed;
      }
    }
    return FALSE;
  }

  /**
   * Setter for OTP secret key.
   *
   * @param string $seed
   *   The OTP secret key.
   */
  public function setSeed($seed) {
    $this->seed = $seed;
  }

  /**
   * Save seed for account.
   *
   * @param string $seed
   *   Un-encrypted seed.
   *
   * @throws \Drupal\encrypt\Exception\EncryptException
   *   Can throw an EncryptException.
   */
  public function storeSeed($seed) {
    // Encrypt seed for storage.
    $encrypted = $this->encryptService->encrypt($seed, $this->encryptionProfile);

    // Until EncryptServiceInterface::encrypt enforces a non-empty string,
    // validate return value is a non-empty string. \base64_encode() below must
    // also only receive a string.
    if (!is_string($encrypted) || strlen($encrypted) === 0) {
      throw new EncryptException('Empty encryption value received from encryption service.');
    }

    $record = [
      'par_tfa_sms_seed' => [
        'seed' => base64_encode($encrypted),
        'created' => $this->time->getRequestTime(),
      ],
    ];

    $this->setUserData('tfa', $record, $this->uid);
  }

  /**
   * {@inheritdoc}
   */
  public function ready() {
    // If a mobile number is stored and the user has a seed generated, then ready.
    if (!empty($this->getMobileNumber()) && $this->getSeed() !== FALSE) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Send the code via the GovUkNotify service.
   * @return boolean
   */
  protected function sendSms() {
    if (!empty($mobile_number = $this->getMobileNumber()) && $this->getSeed() !== FALSE) {
      // Get info from config.
      $config = $this->configFactory->get('govuk_notify.settings');
      $template_id = $config->get('default_sms_template_id');
      $code = $this->generateCode();
      $this->govUkNotifyService->sendSms($mobile_number, $template_id, [
        'code' => $code,
      ]);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getForm(array $form, FormStateInterface $form_state) {
    // Send the SMS.
    if ($this->sendSms()) {
      // Display the verification form.
      $message = $this->t('A code has been sent to your registered mobile device. @mobile_number', [
        '@mobile_number' => $this->getObfuscatedMobileNumber(),
      ]);

      $form['code'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Application verification code'),
        '#description' => $message,
        '#required'  => TRUE,
        '#attributes' => [
          'autocomplete' => 'off',
          'autofocus' => 'autofocus',
        ],
      ];

      $form['actions']['#type'] = 'actions';
      $form['actions']['login'] = [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Verify'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if (!$this->validate($values['code'])) {
      $this->errorMessages['code'] = $this->t('Invalid application code. Please try again.');
      if ($this->alreadyAccepted) {
        $form_state->clearErrors();
        $this->errorMessages['code'] = $this->t('Invalid code, it was recently used for a login. Please try a new code.');
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
      $this->isValid = ($seed && $this->auth->otp->checkTotp($seed, $code, $this->timeSkew));
    }
    return $this->isValid;
  }

  /**
   * {@inheritdoc}
   */
  public function getOverview(array $params) {
    $params['enabled'] = !empty($this->getMobileNumber());
    $output = [
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#value' => $this->t('SMS'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Generate a verification code and send it to your registered mobile device.'),
      ],
      'link' => [
        '#theme' => 'links',
        '#links' => [
          'admin' => [
            'title' => !$params['enabled'] ? $this->t('Set up application') : $this->t('Reset application'),
            'url' => Url::fromRoute('par_tfa_sms.validation.setup', [
              'user' => $this->uid,
            ]),
          ],
        ],
      ],
    ];
    return $output;
  }

  /**
   * Get mobile number for this account.
   *
   * @return string
   *   Decrypted account OTP seed or FALSE if none exists.
   */
  protected function getMobileNumber() {
    // Get the users mobile number from their person data.
    $query = $this->dbConnection->select(
      'par_people_field_data',
      'pfd',
    );
    $query->fields('pfd', ['mobile_phone']);
    $query->condition('pfd.user_id', $this->uid);
    $mobile_number = $query->execute()->fetchField();

    if (!empty($mobile_number)) {
      return $mobile_number;
    }
    return FALSE;
  }

  /**
   * @param $mobile_number
   *
   * @return void
   */
  public function setMobileNumber($mobile_number, $uid): void {
    $this->dbConnection
      ->update('par_people_field_data')
      ->condition('user_id', $uid)
      ->fields([
        'mobile_phone' => $mobile_number,
      ])
      ->execute();
  }

  /**
   * Obfuscate the user mobile number for display.
   *
   * @param $mobile_number
   *
   * @return string
   */
  protected function getObfuscatedMobileNumber() {
    $mobile_number = $this->getMobileNumber();
    if (!empty($mobile_number)) {
      return '********' . substr($mobile_number, -3);
    }
  }

  /**
   * Get the setup form for the validation method.
   *
   * @param array $form
   *   The configuration form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param int $reset
   *   Whether or not the user is resetting the application.
   *
   * @return array
   *   Form API array.
   */
  public function getSetupForm(array $form, FormStateInterface $form_state, $reset = 0) {
    // Generate and store a seed for this user.
    try {
      $this->setSeed($this->createSeed());
      $this->storeSeed($this->seed);
    }
    catch (EncryptException $e) {
    }

    $form['info'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Enter your mobile number and we will send you a verification code to authenticate your device.'),
    ];

    $form['sms_phone_number'] = [
      '#type' => 'tel',
      '#placeholder' => $this->t('Mobile Number'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['login'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Continue'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSetupForm(array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $pattern = "/^(\+44\s?7\d{3}|\(?07\d{3}\)?)\s?\d{3}\s?\d{3}$/";
    $match = preg_match($pattern, $values['sms_phone_number']);

    if ($match == FALSE) {
      $form_state->setErrorByName('sms_phone_number', $this->t(
        'Please enter a valid UK mobile phone number'
      ));
    }

    if (!empty($values['code'])) {
      if (!$this->validateSetup($values['code'])) {
        $this->errorMessages['code'] = $this->t('Invalid application code. Please try again.');
        return FALSE;
      }

      $storage = $form_state->getStorage();
      $storage['code_verified'] = TRUE;
      $form_state->setStorage($storage);
      $this->storeAcceptedCode($values['code']);
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function validateSetup($code) {
    $code = preg_replace('/\s+/', '', $code);
    return $this->auth->otp->checkTotp($this->seed, $code, $this->timeSkew);
  }

  /**
   * {@inheritdoc}
   */
  public function submitSetupForm(array $form, FormStateInterface $form_state) {
    if (empty($this->uid)) {
      $values = $form_state->getValues();
      $this->uid = $values['account']->uid->value;
    }

    $user = $this->userStorage->load($this->uid);
    if (!empty($user) && $user->hasField('mobile_number')) {
      $user->set('mobile_number', $form_state->getValue('sms_phone_number'));
      $user->save();

      // Enable SMS plugin in the userData.
      $this->tfaSaveTfaData($this->uid, [
        'plugins' => 'par_tfa_sms',
      ]);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Generate a code to send in the SMS.
   *
   * @return string
   */
  protected function generateCode() {
    $otp = new Otp();

    // Get the seed associated with the current user.
    $seed = $this->getSeed();

    // Generate the code.
    return $otp->totp($seed);
  }

}
