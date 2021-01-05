<?php

namespace Drupal\par_error_handling\EventSubscriber;

use Drupal\Core\Site\Settings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ErrorPageSubscriber implements EventSubscriberInterface {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::EXCEPTION][] = ['onException', 60];
    return $events;
  }

  /**
   * Handles the exception and displays a friendly error page.
   *
   * @param GetResponseForExceptionEvent $event
   */
  public function onException(GetResponseForExceptionEvent $event) {
    $exception = $event->getException();
    $file_path = dirname(__FILE__) . '/../../assets/error.html';
    $html = file_get_contents($file_path);

    $error_level = \Drupal::configFactory()->get('system.logging')->get('error_level');

    // Log all errors with a custom code.
    $log = \Drupal::logger('par_exception');
    $custom_code = substr(uniqid(), -7, -1);
    $log->warning($custom_code . ': ' . $event->getException()->getMessage());

    $handle_error = true;

    // Don't handle common treatable errors.
    $ignore = ['403', '404'];
    if ($exception instanceof HttpExceptionInterface) {
      if (in_array($exception->getStatusCode(), $ignore)) {
        $handle_error = false;
      }
    }

    // Only show friendly error messages if verbose reporting is disabled.
    if ($html && $handle_error && $error_level !== ERROR_REPORTING_DISPLAY_VERBOSE) {
      return $event->setResponse(new Response($html));
    }
  }

}
