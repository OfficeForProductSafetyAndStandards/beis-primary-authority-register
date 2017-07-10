<?php

namespace Drupal\par_styleguide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for Pagerer example.
 */
class StyleguidePaginationController extends ControllerBase {

  /**
   * Build the pagerer example page.
   *
   * @return array
   *   A render array.
   */
  public function content() {

    $build = [];

    $build['guidance_heading'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Guidance") . '</h2>'];

    $build['guidance_heading_pagination'] = ['#markup' => '<h3 class="heading-small">' . $this->t("Try to make it so people don't need pagination") . '</h3>'];
    $build['guidance_heading_pagination_item_1'] = ['#markup' => '<p>' . $this->t("Paginating lists or search results comes at a cost because you're hiding information from users.") . '</p>'];
    $build['guidance_heading_pagination_item'] = ['#markup' => '<p>' . $this->t("Set the default number of results per page so that pagination is rarely needed.") . '</p>'];
    $build['guidance_heading_pagination_item_3'] = ['#markup' => '<p>' . $this->t("Use analytics to establish how far down the list users tend to still find relevant results.") . '</p>'];
    $build['guidance_heading_pagination_item_4'] = ['#markup' => '<p>' . $this->t("Use this to decide how many results per page to show.") . '</p>'];

    $build['guidance_heading_infinte'] = ['#markup' => '<h3 class="heading-small">' . $this->t("Avoid infinite scroll") . '</h3>'];

    $build['guidance_heading_guides'] = ['#markup' => '<h3 class="heading-small">' . $this->t("Pagination on guides") . '</h3>'];
    $build['guidance_heading_guides_item_1'] = ['#markup' => '<p>' . $this->t("For guides that are intended to be read in a certain order, use previous and next links.") . '</p>'];

    $build['main_pager_title'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Main pager") . '</h2>'];

    // Get a data table for the pagination element to page.
    $header = array(
      array('data' => 'name'),
      array('data' => 'path'),
    );
    $query = db_select('router', 'd')->extend('Drupal\Core\Database\Query\PagerSelectExtender')->element(2);
    $result = $query
      ->fields('d', array('name', 'path'))
      ->limit(10)
      ->orderBy('d.name')
      ->execute();
    $rows = array();
    foreach ($result as $row) {
      $rows[] = array('data' => (array) $row);
    }

    $build['main_pager'] = [
      '#type' => 'pager',
      '#theme' => 'pagerer',
      '#element' => 2,
      '#config' => [
        'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
      ],
    ];

    return $build;
  }

}
