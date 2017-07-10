<?php

namespace Drupal\par_styleguide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for Pagerer example.
 */
class StyleguideDataController extends ControllerBase {

  /**
   * Build the pagerer example page.
   *
   * @return array
   *   A render array.
   */
  public function content() {

    // Data table - lists routes registered.
    $header = [
      ['data' => 'name'],
      ['data' => 'path'],
    ];
    $query = db_select('router', 'd')->extend('Drupal\Core\Database\Query\PagerSelectExtender')->element(2);
    $result = $query
      ->fields('d', ['name', 'path'])
      ->limit(10)
      ->orderBy('d.name')
      ->execute();
    $rows = [];
    foreach ($result as $row) {
      $rows[] = ['data' => (array) $row];
    }

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
