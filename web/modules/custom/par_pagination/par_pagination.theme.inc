<?php

/**
 * @file
 * Contains par_data_entities.module.
 */

/**
 * Prepares variables for views PAR Pagination template.
 *
 * Default template: templates/views-par-pager.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - tags: Provides link text for the next/previous links.
 *   - element: The pager's id.
 *   - parameters: Any extra GET parameters that should be retained, such as
 *     exposed input.
 */
function template_preprocess_views_par_pager(&$variables) {

  global $pager_page_array, $pager_total;

  $tags = &$variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];

  // Current is the page we are currently paged to.
  $variables['items']['current'] = $pager_page_array[$element] + 1;

  if ($pager_total[$element] > 1 && $pager_page_array[$element] > 0) {
    $options = [
      'query' => pager_query_add_page($parameters, $element, $pager_page_array[$element] - 1),
    ];
    $variables['items']['previous']['href'] = \Drupal::url('<current>', [], $options);
    if (isset($tags[1])) {
      $variables['items']['previous']['text'] = $tags[1];
    }
    $variables['items']['previous']['attributes'] = new Attribute();
  }

  if ($pager_page_array[$element] < ($pager_total[$element] - 1)) {
    $options = [
      'query' => pager_query_add_page($parameters, $element, $pager_page_array[$element] + 1),
    ];
    $variables['items']['next']['href'] = \Drupal::url('<current>', [], $options);
    if (isset($tags[3])) {
      $variables['items']['next']['text'] = $tags[3];
    }
    $variables['items']['next']['attributes'] = new Attribute();
  }

  // This is based on the entire current query string. We need to ensure
  // cacheability is affected accordingly.
  $variables['#cache']['contexts'][] = 'url.query_args';

}
