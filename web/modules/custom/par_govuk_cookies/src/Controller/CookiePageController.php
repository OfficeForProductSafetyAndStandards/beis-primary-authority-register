<?php

namespace Drupal\par_govuk_cookies\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class CookiePageController extends ControllerBase {

  /**
   * The main cookie consent page.
   */
  public function content() {
    $config = \Drupal::config('system.site');

    $build = [
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Cookies are small files saved on your phone, tablet or computer when you visit a website.'),
      ],
      [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('We use cookies to make @site_name work and collect information about how you use our service.',
          ['@site_name' => $config->get('name')]),
      ],
    ];

    return $build;
  }

}
