<?php

namespace Drupal\par_data_entities\ParamConverter;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;


class ParEntityConverter implements ParamConverterInterface {

  /**
   * Entity manager which performs the upcasting in the end.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The inner parent service being decorated.
   *
   * @param EntityConverter
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
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The conig factory.
   */
  public function __construct($parent, EntityManagerInterface $entity_manager, $config_factory) {
    $this->entityManager = $entity_manager;
    $this->parent = $parent;
    $this->settings = $config_factory->get('par_data_entities.settings');
  }

  public function __call($method, $args) {
    return call_user_func_array(array($this->parent, $method), $args);
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    // Get stubs and generate a dummy entity.
    if ($this->settings->get('stubbed')) {

      // Set the entity ID to use.
      $entity_type_id = $entity_type_id = substr($definition['type'], strlen('entity:'));
      if ($storage = $this->entityManager->getStorage($entity_type_id)) {
        $stub = $this->settings->get('stubs')[$entity_type_id];
        if (is_numeric($stub)) {
          $entity = $this->parent->convert($value, $definition, $name, $defaults);
        }
        else {
          $entity = current($storage->loadByProperties([
            'uuid' => $this->settings->get('stubs')[$entity_type_id]
          ]));
        }
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


    if ($this->settings->get('stubbed')) {
      return TRUE;
    }
    else {
      return $this->parent->applies($definition, $name, $route);
    }
  }

}
