<?php

namespace Drupal\par_subscriptions\Views;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for subscriptions.
 */
class ParSubscriptionsViewsData extends EntityViewsData implements EntityViewsDataInterface {

  public function getParSubscriptionsManager() {
    return \Drupal::service('par_subscriptions.manager');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getViewsData() {
    $data = parent::getViewsData();

    return $data;
  }

}
