<?php

namespace Drupal\par_cookies\Form;

use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Drupal\Component\Serialization\Json;

/**
 * A form controller for the cookie page.
 */
class CookieConsentForm extends FormBase {

  const ALLOW_VALUE = 'allow';
  const BLOCK_VALUE = 'block';
  const COOKIE_NAME = 'cookie_policy';

  /**
   * Cookie types.
   */
  protected $types = ['functional', 'analytics', 'other'];

  /**
   * The flood service.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * Constructs a cookie page controller.
   *
   * @param \Drupal\Core\Flood\FloodInterface $flood
   *   The flood service.
   */
  public function __construct(FloodInterface $flood) {
    $this->flood = $flood;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flood')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cookie_consent_policy';
  }

  /**
   * Subscribe to a list.
   *
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $list = NULL, $subscription_status = NULL) {
    $service = \Drupal::config('system.site')->get('name');
    $cookie = \Drupal::request()->cookies->get(self::COOKIE_NAME);
    var_dump($cookie);

    foreach ($this->types as $type) {
      $options = [
        self::ALLOW_VALUE => 'Yes',
        self::BLOCK_VALUE => 'No',
      ];
      $form[$type] = [
        '#type' => 'radios',
        '#title' => "Do you want to accept $type cookies?",
        '#options' => $options,
        '#default_value' => current($options),
      ];
    }


    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Add flood protection for unauthenticated users.
    $fid = implode(':', [$this->getRequest()->getClientIP(), $this->currentUser()->id()]);
    if ($this->currentUser()->isAnonymous() &&
      !$this->flood->isAllowed("par_cookies.{$this->getFormId()}", 10, 3600, $fid)) {
      $form_state->setErrorByName('text', $this->t(
        'Too many form submissions from your location.
        This IP address is temporarily blocked. Please try again later.'
      ));
      return;
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Register flood protection.
    $fid = implode(':', [$this->getRequest()->getClientIP(), $this->currentUser()->id()]);
    $this->flood->register("par_cookies.{$this->getFormId()}", 3600, $fid);

    $cookie_policy = [];
    foreach ($this->types as $type) {
      if ($form_state->getValue($type) === CookieConsentForm::ALLOW_VALUE) {
        $cookie_policy[] = $type;
      }
    }

    // Set the new cookie policy.
    $name = 'cookie_policy';
    $value = Json::encode($cookie_policy);
    $expiry = 60*60*24*365;
    $cookie = new Cookie($name, $value, $expiry);

    $response = $form_state->getResponse();
    $response->headers->setCookie($cookie);
    return $response;
  }
}
