<?php

namespace Drupal\par_flows;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the default class to build a listing of configuration entities.
 *
 * @ingroup entity_api
 */
class FlowListBuilder extends ConfigEntityListBuilder {

  /**
   * Get the renderer service.
   *
   * @return \Drupal\Core\Render\RendererInterface
   */
  public function getRenderer() {
    return \Drupal::service('renderer');
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];

    $header['flow'] = t('User Journey');
    $header['description'] = t('Description');

    $header['routes'] = t('Pages');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];

    $row['flow'] = $entity->label();
    $row['description'] = $entity->getDescription();

    $routes = [];
    foreach ($entity->getSteps() as $key => $step) {
      $routes[$key] = $step['route'] ?? '';
    }
    $route_list = [
      '#theme' => 'item_list',
      '#list_type' => 'ol',
      '#items' => array_filter($routes),
      '#attributes' => ['class' => ['govuk-list', 'govuk-form-group']],
    ];
    $row['routes'] = $this->getRenderer()->render($route_list);

    return $row + parent::buildRow($entity);
  }

}
