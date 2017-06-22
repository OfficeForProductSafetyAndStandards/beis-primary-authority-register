<?php

namespace Drupal\par_styleguide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
* A controller for all styleguide page output.
*/
class StyleguideController extends ControllerBase {

  /**
  * The main index page for the styleguide.
  */
  public function index() {

    $build = [
      '#theme' => 'par_styleguide',
    ];

    return $build;
  }

  /**
   * The main page for showing all useful form field types.
   */
  public function forms() {

    $form['textfield'] = array(
      '#type' => 'textfield',
      '#title' => t('Basic textfield'),
      '#required' => TRUE,
      '#prefix' => '<form>',
      '#suffix' => '(suffix)',
    );
    $form['checkbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Boolean checkbox'),
      '#prefix' => '(prefix)',
      '#suffix' => '(suffix)',
    );
    $form['textarea'] = array(
      '#type' => 'textarea',
      '#title' => t('Basic textarea'),
      '#prefix' => '(prefix)',
      '#suffix' => '(suffix)',
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit button'),
      '#prefix' => '(prefix)',
      '#suffix' => '(suffix)',
    );

    return $form;
  }

}