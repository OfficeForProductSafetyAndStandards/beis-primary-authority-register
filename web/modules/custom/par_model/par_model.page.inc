<?php

/**
 * @file
 * Contains par_business.page.inc..
 *
 * Page callback for par_business entities.
 */

use Drupal\Core\Url;

/**
 * Prepares variables for custom entity type creation list templates.
 *
 * Default template: par_business-content-add-list.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array of par_business types.
 *
 * @see block_content_add_page()
 */
function template_preprocess_par_entities_content_add_list(&$variables) {
  $variables['types'] = [];
  $query = \Drupal::request()->query->all();
  foreach ($variables['content'] as $type) {
    $url = new Url('entity.' . $type->getEntityType()->getBundleOf() . '.add_form', [
      $type->getEntityType()->id() => $type->id(),
    ], ['query' => $query]);
    $variables['types'][$type->id()] = [
      'url' => $url->toString(),
      'label' => $type->label(),
      'description' => $type->getDescription(),
    ];
  }
}
