<?php

namespace Drupal\par_styleguide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for Pagerer example.
 */
class StyleguideDataController extends ControllerBase {

  /**
   * Build the data (tables) example page.
   *
   * @return array
   *   A render array.
   */
  public function content() {

    // Table headers.
    $header = [
      ['data' => 'Primary Authority'],
      ['data' => 'Business'],
      ['data' => 'PA Ts&Cs'],
      ['data' => 'PA Details'],
      ['data' => 'PA Docs'],
      ['data' => 'Bus. Ts&Cs'],
      ['data' => 'Bus. Details']
    ];

    // Table data/cells.
    $rows = [
      ['Westminster City Council', 'Selfridges', 'Confirmed 07.08.17', 'Confirmed 07.08.17', 'Confirmed 07.08.17', 'Confirmed 10.08.17', 'Confirmed 10.08.17'],
      ['Another County Council', 'Another Business', 'Confirmed 07.08.17', 'Confirmed 07.08.17', '80% confirmed 07.08.17', 'Confirmed 10.08.17', 'Confirmed 10.08.17'],
      ['Another County Council', 'Another Business', 'Confirmed 07.08.17', 'Confirmed 07.08.17', '80% confirmed 07.08.17', 'Confirmed 10.08.17', 'Confirmed 10.08.17'],
      ['Another County Council', 'Another Business', 'Confirmed 07.08.17', 'Confirmed 07.08.17', '80% confirmed 07.08.17', 'Invite sent 07.08.17', 'Not confirmed'],
      ['Another County Council', 'Another Business', 'Confirmed 07.08.17', 'Not confirmed', 'Not confirmed', 'Invite sent 07.08.17', 'Not confirmed'],
    ];

    // Create a render array ($build) which will be themed for output.
    $build = [];

    // First table.
    $build['basic_table_title'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Basic data table") . '</h2>'];
    $build['basic_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t("No results could be found."),
    ];

    // Empty table.
    $build['empty_table_title'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Empty data table") . '</h2>'];
    $build['empty_table_description'] = ['#markup' => '<p>' . $this->t("This is an example of a data table which has no results.") . '</p>'];
    $build['empty_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => [],
      '#empty' => $this->t("No results could be found."),
    ];

    return $build;

  }

}
