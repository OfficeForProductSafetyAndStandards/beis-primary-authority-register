<?php

namespace Drupal\par_error_handling\EventSubscriber;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Utility\Error;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ErrorPageSubscriber implements EventSubscriberInterface {

  /**
   * @var string
   *
   * One of the error level constants defined in bootstrap.inc.
   */
  protected $errorLevel;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new FinalExceptionSubscriber.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Gets the configured error level.
   *
   * @return string
   */
  protected function getErrorLevel() {
    if (!isset($this->errorLevel)) {
      $this->errorLevel = $this->configFactory->get('system.logging')->get('error_level');
    }
    return $this->errorLevel;
  }

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Run as one of the last subscribers.
    $events[KernelEvents::EXCEPTION][] = ['onException', -200];
    return $events;
  }

  /**
   * Handles the exception and displays a friendly error page.
   *
   * @param ExceptionEvent $event
   */
  public function onException(ExceptionEvent $event) {
    $exception = $event->getException();
    $error = Error::decodeException($exception);

    // Log all errors with a custom code.
    $log = \Drupal::logger('par_exception');
    $custom_code = substr(uniqid(), -7, -1);

    // Always log verbose errors for our private record.
    $message = new FormattableMarkup(
      '%par_code [%type]: @message in %function (line %line of %file).',
      ['%par_code' => $custom_code] + $error
    );
    $log->error($message);

    $content_type = $event->getRequest()->getRequestFormat() == 'html' ? 'text/html' : 'text/plain';
    $file_path = dirname(__FILE__) . '/../../assets/error.html';
    $html = file_get_contents($file_path);
    $response = new Response($html, 500, ['Content-Type' => $content_type]);

    if ($exception instanceof HttpExceptionInterface) {
      $response->setStatusCode($exception->getStatusCode());
      $response->headers->add($exception->getHeaders());
    }
    else {
      $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR, '500 Service unavailable (with message)');
    }

    // Only show friendly error messages if verbose reporting is disabled.
    if ($html && !$this->isErrorLevelVerbose()) {
      $event->setResponse($response);
    }
  }

  /**
   * Checks whether the error level is verbose or not.
   *
   * @return bool
   */
  protected function isErrorLevelVerbose() {
    return $this->getErrorLevel() === ERROR_REPORTING_DISPLAY_VERBOSE;
  }

}
