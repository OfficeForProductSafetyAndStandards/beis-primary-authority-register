<?php

namespace Drupal\govuk_notify_tfa\Plugin\TfaValidation;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tfa\Plugin\TfaSendInterface;
use ParagonIE\ConstantTime\Encoding;
use Drupal\Core\Form\FormStateInterface;
use Drupal\encrypt\EncryptionProfileManagerInterface;
use Drupal\encrypt\EncryptServiceInterface;
use Drupal\tfa\Plugin\TfaBasePlugin;
use Drupal\tfa\Plugin\TfaValidationInterface;
use Otp\Otp;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * GovUK Notify send class for issuing SMS codes.
 *
 * @TfaSend(
 *   id = "tfa_sms_send",
 *   label = @Translation("Notify SMS delivery"),
 *   description = @Translation("GovUK Notify SMS Send Plugin"),
 * )
 */
class GovukNotifySmsLoginSend extends GovukNotifySmsLoginValidation implements TfaSendInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}}
   */
  public function begin() {
    $this->send();
  }
}
