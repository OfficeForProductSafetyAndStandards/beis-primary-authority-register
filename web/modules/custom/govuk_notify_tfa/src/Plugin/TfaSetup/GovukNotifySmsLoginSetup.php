<?php

namespace Drupal\govuk_notify_tfa\Plugin\TfaSetup;

use Drupal\govuk_notify\NotifyService\NotifyServiceInterface;
use Drupal\govuk_notify_tfa\Plugin\TfaValidation\GovukNotifySmsLoginValidation;
use ParagonIE\ConstantTime\Encoding;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\encrypt\EncryptionProfileManagerInterface;
use Drupal\encrypt\EncryptServiceInterface;
use Drupal\tfa\Plugin\TfaSetupInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserDataInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
/**
 * TOTP setup class to setup TOTP validation.
 *
 * @TfaSetup(
 *   id = "tfa_sms_setup",
 *   label = @Translation("Notify SMS"),
 *   description = @Translation("GovUK Notify SMS Setup Plugin"),
 *   setupMessages = {
 *    "saved" = @Translation("Application code verified."),
 *    "skipped" = @Translation("Application codes not enabled.")
 *   }
 * )
 */
class GovukNotifySmsLoginSetup extends GovukNotifySmsLoginValidation implements TfaSetupInterface {
  use StringTranslationTrait;

  /**
   * Un-encrypted seed.
   *
   * @var string
   */
  protected $seed;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UserDataInterface $user_data, EncryptionProfileManagerInterface $encryption_profile_manager, EncryptServiceInterface $encrypt_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $user_data, $encryption_profile_manager, $encrypt_service);
    // Generate seed.
    $this->setSeed($this->createSeed());
  }

  /**
   * {@inheritdoc}
   */
  public function getSetupForm(array $form, FormStateInterface $form_state) {
    $form['phone_number'] = [
      '#type' => 'textfield',
      '#title' => 'Mobile phone number',
      '#value' => $this->getPhone(),
      '#description' => $this->t('Enter your phone number.'),
      '#required'  => TRUE,
    ];

    $form['seed'] = [
      '#type' => 'hidden',
      '#value' => $this->seed,
    ];

    // Include code entry form.
    if ($this->getPhone()) {
      $form = $this->getForm($form, $form_state);
    }
    else {
      $form['actions']['#type'] = 'actions';
      $form['actions']['send'] = [
        '#type'  => 'submit',
        '#name' => 'send',
        '#value' => t('Send code'),
        '#limit_validation_errors' => [],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSetupForm(array $form, FormStateInterface $form_state) {
    // Do not validate if the code is being sent.
    if ($form_state->getTriggeringElement()['#name'] === 'send') {
      return TRUE;
    }

    if (!$this->validate($form_state->getValue('code'))) {
      $this->errorMessages['code'] = $this->t('Invalid application code. Please try again.');
      // $form_state->setErrorByName('code', $this->errorMessages['code']);.
      return FALSE;
    }
    $this->storeAcceptedCode($form_state->getValue('code'));
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function validate($code) {
    $code = preg_replace('/\s+/', '', $code);
    return $this->otp->checkTotp(Encoding::base32DecodeUpper($this->seed), $code, $this->timeSkew);
  }

  /**
   * {@inheritdoc}
   */
  public function submitSetupForm(array $form, FormStateInterface $form_state) {
    // Write seed for user.
    $this->storeSeed($this->seed);

    // Store the user phone number.
    $this->setPhone($form_state->getValue('phone_number'));
    return TRUE;
  }

  /**
   * Create OTP seed for account.
   *
   * @return string
   *   Un-encrypted seed.
   */
  protected function createSeed() {
    return $this->generateRandom();
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
   * Creates a pseudo random Base32 string
   *
   * This could decode into anything. It's located here as a small helper
   * where code that might need base32 usually also needs something like this.
   *
   * @param integer $length Exact length of output string
   *
   * @return string Base32 encoded random
   */
  public static function generateRandom($length = 16) {
    $keys = array_merge(range('A','Z'), range(2,7)); // No padding char

    $string = '';

    for ($i = 0; $i < $length; $i++) {
      $string .= $keys[random_int(0, 31)];
    }

    return $string;
  }

  /**
   * Get account name for QR image.
   *
   * @return string
   *   URL encoded string.
   */
  protected function accountName() {
    /** @var User $account */
    $account = User::load($this->configuration['uid']);
    $prefix = $this->siteNamePrefix ? preg_replace('@[^a-z0-9-]+@','-', strtolower(\Drupal::config('system.site')->get('name'))) : $this->namePrefix;
    return urlencode($prefix . '-' . $account->getUsername());
  }

  /**
   * {@inheritdoc}
   */
  public function getOverview($params) {
    $plugin_text = $this->t('Validation Plugin: @plugin',
      [
        '@plugin' => str_replace(' Setup', '', $this->getLabel()),
      ]
    );
    $output = [
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('TFA application'),
      ],
      'validation_plugin' => [
        '#type' => 'markup',
        '#markup' => '<p>' . $plugin_text . '</p>',
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Generate verification codes to send to a mobile device.'),
      ],
      'link' => [
        '#theme' => 'links',
        '#links' => [
          'admin' => [
            'title' => !$params['enabled'] ? $this->t('Set up application') : $this->t('Reset application'),
            'url' => Url::fromRoute('tfa.validation.setup', [
              'user' => $params['account']->id(),
              'method' => $params['plugin_id'],
            ]),
          ],
        ],
      ],
    ];
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getHelpLinks() {
    return $this->pluginDefinition['helpLinks'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSetupMessages() {
    return ($this->pluginDefinition['setupMessages']) ?: '';
  }

}
