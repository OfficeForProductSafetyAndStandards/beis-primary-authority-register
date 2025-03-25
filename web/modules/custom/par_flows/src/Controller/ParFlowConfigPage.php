<?php

namespace Drupal\par_flows\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form controller for subscription lists.
 */
class ParFlowConfigPage extends ControllerBase  {

  /**
   * The flow storage.
   *
   * @var \Drupal\par_flows\ParFlowStorage
   */
  protected $flowStorage;

  /**
   * The response cache kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * Constructs a subscription controller for rendering requests.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $kill_switch
   *   The cache kill switch service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, KillSwitch $kill_switch) {
    $this->flowStorage = $entity_type_manager->getStorage('par_flow');
    $this->killSwitch = $kill_switch;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * Get the flow storage to list all user journeys.
   *
   * @return \Drupal\par_flows\ParFlowStorage
   */
  private function getFlowStorage() {
    return $this->flowStorage ?? \Drupal::service('entity_type.manager')->getStorage('par_flow');
  }

  /**
   * Get the cache kill service.
   *
   * @return \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  private function getKillSwitch() {
    return $this->killSwitch ?? \Drupal::service('entity_type.manager')->getStorage('par_flow');
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = []) {

  }
}
