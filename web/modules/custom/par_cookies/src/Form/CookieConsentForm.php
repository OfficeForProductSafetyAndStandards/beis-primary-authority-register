<?php

namespace Drupal\par_cookies\Form;

use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidator;
use Drupal\Core\Routing\LocalRedirectResponse;
use Drupal\Core\Url;
use Drupal\par_flows\ParFlowException;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Serialization\Json;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * A form controller for the cookie page.
 */
class CookieConsentForm extends FormBase {

  const ALLOW_VALUE = 'allow';
  const BLOCK_VALUE = 'block';
  const COOKIE_NAME = '_cookie_policy';

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
   * The path validator service.
   *
   * @var \Drupal\Core\Path\PathValidator
   */
  protected $pathValidator;

  /**
   * Constructs a cookie page controller.
   *
   * @param \Drupal\Core\Flood\FloodInterface $flood
   *   The flood service.
   *
   * @param \Drupal\Core\Path\PathValidator $path_validator
   *   The path validator service.
   */
  public function __construct(FloodInterface $flood, PathValidator $path_validator) {
    $this->flood = $flood;
    $this->pathValidator = $path_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flood'),
      $container->get('path.validator')
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


    $cookie = $this->getRequest()->cookies->get(self::COOKIE_NAME);
    $cookies = $this->getRequest()->cookies->get(self::COOKIE_NAME);
    var_dump($cookies);
    $referer = $this->getRequest()->headers->get('referer');

    foreach ($this->types as $type) {
      $options = [
        self::ALLOW_VALUE => 'Yes',
        self::BLOCK_VALUE => 'No',
      ];
      $form[$type] = [
        '#type' => 'radios',
        '#title' => "Do you want to accept $type cookies?",
        '#options' => $options,
        '#default_value' => self::ALLOW_VALUE,
      ];
    }

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#submit' => ['::submitForm'],
      '#value' => $this->t('Save cookie settings'),
      '#attributes' => [
        'class' => ['cta-submit', 'govuk-button'],
        'data-prevent-double-click' => 'true',
        'data-module' => 'govuk-button',
      ],
    ];

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

    $response = $form_state->getResponse() ??
      new RedirectResponse($this->getRequest()->getUri());
    // Set the new cookie policy.
    $response->headers->setCookie(new Cookie(
      self::COOKIE_NAME,
      Json::encode($cookie_policy),
      \Drupal::time()->getRequestTime() + 31536000,
      '/',
      ".{$this->getRequest()->getHost()}",
      false,
      false,
      true,
    ));

    $form_state->setResponse($response);
  }
}
