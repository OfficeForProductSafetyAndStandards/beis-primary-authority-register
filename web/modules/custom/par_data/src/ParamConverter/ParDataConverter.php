<?php

namespace Drupal\par_data\ParamConverter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\Routing\Route;


class ParDataConverter implements ParamConverterInterface {

  /**
   * Entity manager which performs the upcasting in the end.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $parDataManager;

  /**
   * The inner parent service being decorated.
   *
   * @param \Drupal\Core\ParamConverter\EntityConverter
   */
  protected $parent;

  /**
   * Settings for stubbed data.
   *
   * @param boolean
   */
  protected $settings;

  /**
   * Constructs a new EntityConverter.
   *
   * @param \Drupal\Core\ParamConverter\EntityConverter $parent
   *   The param converter that is being decorated.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The conig factory.
   */
  public function __construct($parent, EntityTypeManagerInterface $entity_type_manager, ParDataManagerInterface $par_data_manager, $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->parDataManager = $par_data_manager;
    $this->parent = $parent;
    $this->settings = $config_factory->get('par_data.settings');
  }

  public function __call($method, $args) {
    return call_user_func_array(array($this->parent, $method), $args);
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    $par_data_manager = \Drupal::service('par_data.manager');

    // Get stubs and generate a dummy entity.
    if (array_key_exists($name, $par_data_manager->getParEntityTypes()) && $this->settings->get('stubbed')) {
      // Set the entity Type to use.
      $entity_type_id = substr($definition['type'], strlen('entity:'));

      // Set the entity ID to use.
      if ($this->parDataManager->getEntityTypeStorage($entity_type_id)->load($value)) {
        $entity = $this->parent->convert($value, $definition, $name, $defaults);
      }
      elseif ($entity_type_id) {
        $entities = $this->parDataManager->getEntityTypeStorage($entity_type_id)->loadMultiple();
        $entity = !empty($entities) ? current($entities) : NULL;
      }

      return isset($entity) ? $entity : NULL;
    }
    else {
      return $this->parent->convert($value, $definition, $name, $defaults);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    $par_data_manager = \Drupal::service('par_data.manager');
    if (array_key_exists($name, $par_data_manager->getParEntityTypes()) && $this->settings->get('stubbed')) {
      return TRUE;
    }
    else {
      return $this->parent->applies($definition, $name, $route);
    }
  }

}
