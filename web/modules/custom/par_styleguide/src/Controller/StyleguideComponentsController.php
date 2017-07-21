<?php

namespace Drupal\par_styleguide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for Pagerer example.
 */
class StyleguideComponentsController extends ControllerBase {

  /**
   * Build the reusable components example page.
   *
   * @return array
   *   A render array.
   */
  public function content() {

    // Create a render array ($build) which will be themed for output.
    $build = [];

    // Business Name & Address.
    $build['business_name_address_header'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Business Name/Address") . '</h2>'];
    $build['business_name_address'] = [
      '#theme' => 'par_components_business_name_address',
      '#name' => 'Selfridges & Co',
      '#address' => '400 Oxford Street, London, W1A 1AB',
      '#prefix' => '<div class="styleguide-example">',
      '#suffix' => '</div>'
    ];

    // Business Primary Contact.
    $build['business_primary_contact_address_header'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Primary Contact") . '</h2>'];
    $build['business_primary_contact_address'] = [
      '#theme' => 'par_components_business_primary_contact',
      // @todo change to getter fn.
      '#name' => 'Jasper Thomas',
      '#role' => 'CTO',
      '#telephone' => '0207 111 1111',
      '#email' => 'jasper.thomas@example.com',
      '#prefix' => '<div class="styleguide-example">',
      '#suffix' => '</div>'
    ];

    return $build;

  }

}
