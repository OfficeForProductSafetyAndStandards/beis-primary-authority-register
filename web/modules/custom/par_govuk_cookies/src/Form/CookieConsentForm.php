<?php

namespace Drupal\par_govuk_cookies\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CookieConsentForm extends FormBase {

  const ALLOW_VALUE = 'allow';
  const BLOCK_VALUE = 'block';

  /**
   * CookieConsentForm Constructor.
   */
  public function __construct(
    protected FloodInterface $flood,
    protected PathValidatorInterface $pathValidator,
    protected $configFactory,
    protected $requestStack,
  ) {

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flood'),
      $container->get('path.validator'),
      $container->get('config.factory'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cookie_consent_policy';
  }

  /**
   * {@inheritdoc}
   */
  public function getCookieName() {
    return $this->configFactory->get('par_govuk_cookies.settings')->get('name');
  }

  /**
   * {@inheritdoc}
   */
  public function getCookieTypes() {
    return $this->configFactory->get('par_govuk_cookies.settings')->get('types');
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $list = NULL, $subscription_status = NULL) {
    $request = $this->requestStack->getCurrentRequest();
    $cookie_name = $this->getCookieName();

    if (!empty($cookie_name) && $request->cookies->has($cookie_name)) {
      $cookie = $request->cookies->get($cookie_name);
      $cookie_policy = Json::decode($cookie);
    }
    else {
      $cookie_policy = $this->getCookieTypes();
    }

    foreach ($this->getCookieTypes() as $type) {

      if ($type !== 'essential') {
        $options = [
          self::ALLOW_VALUE => 'Yes',
          self::BLOCK_VALUE => 'No',
        ];
        $form[$type] = [
          '#type' => 'radios',
          '#title' => "Do you want to accept $type cookies?",
          '#options' => $options,
          '#default_value' => $cookie_policy[$type] !== false ?
            self::ALLOW_VALUE :
            self::BLOCK_VALUE,
        ];
      } else {
        $form[$type] = [
          '#type' => 'checkbox',
          '#title' => "Essential cookies",
          '#options' => 'true',
          '#attributes' => array('checked' => 'checked'),
          '#disabled' => TRUE,
          '#wrapper_attributes' => [
            'class' => [
              'visually-hidden',
            ],
          ],
        ];

      }
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
      !$this->flood->isAllowed("govuk_cookies.{$this->getFormId()}", 100, 3600, $fid)) {
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
    $this->flood->register("govuk_cookies.{$this->getFormId()}", 3600, $fid);

    $cookie_policy = [];
    foreach ($this->getCookieTypes() as $type) {

      if ($type !== 'essential') {
        if ($form_state->getValue($type) === self::ALLOW_VALUE) {
          $cookie_policy[$type] = true;
        } else {
          $cookie_policy[$type] = false;
        }
      } else {
        $cookie_policy[$type] = true;
      }
    }

    $response = $form_state->getResponse() ??
      new RedirectResponse($this->getRequest()->getUri());

    // Set the new cookie policy.
    $response->headers->setCookie(new Cookie(
      $this->getCookieName(),
      Json::encode($cookie_policy),
      \Drupal::time()->getRequestTime() + 31536000,
      '/',
      "",
      false,
      false,
      true,
      'strict',
    ));

    $this->messenger()->addMessage($this->t('Your cookie preferences have been updated.'));
    $form_state->setResponse($response);
  }
}
