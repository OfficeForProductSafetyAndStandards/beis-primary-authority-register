<?php

namespace Drupal\govuk_notify_tfa\Plugin\TfaSetup;

use Drupal\govuk_notify_tfa\Plugin\TfaValidation\GovukNotifySmsLoginSend;
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
use chillerlan\QRCode\QRCode;
/**
 * TOTP setup class to setup TOTP validation.
 *
 * @TfaSetup(
 *   id = "tfa_notify_sms_setup",
 *   label = @Translation("Notify SMS Setup"),
 *   description = @Translation("GovUK Notify SMS Setup Plugin"),
 *   setupMessages = {
 *    "saved" = @Translation("Application code verified."),
 *    "skipped" = @Translation("Application codes not enabled.")
 *   }
 * )
 */
class GovukNotifySmsLoginSetup extends GovukNotifySmsLoginSend implements TfaSetupInterface {
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
      '#type' => 'telephone',
      '#value' => $this->seed,
      '#description' => $this->t('Enter your phone number.'),
    ];

    // Include code entry form.
    $form = $this->getForm($form, $form_state);
    $form['actions']['login']['#value'] = $this->t('Verify and save');

    // Alter code description.
    $form['code']['#description'] = $this->t('A verification code will be sent to the telephone number you entered. The verification code is six digits long.');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateSetupForm(array $form, FormStateInterface $form_state) {
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
    return $this->auth->otp->checkTotp(Encoding::base32DecodeUpper($this->seed), $code, $this->timeSkew);
  }

  /**
   * {@inheritdoc}
   */
  public function submitSetupForm(array $form, FormStateInterface $form_state) {
    // Write seed for user.
    $this->storeSeed($this->seed);
    return TRUE;
  }

  /**
   * Get a base64 qrcode image uri of seed.
   *
   * @return string
   *   QR-code uri.
   */
  protected function getQrCodeUri() {
    return (new QRCode)->render('otpauth://totp/' . $this->accountName() . '?secret=' . $this->seed . '&issuer=' . $this->issuer);
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
   * Setter for OTP secret key.
   *
   * @param string $seed
   *   The OTP secret key.
   */
  public function setSeed($seed) {
    $this->seed = $seed;
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
        '#value' => $this->t('Generate verification codes from a mobile or desktop application.'),
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
