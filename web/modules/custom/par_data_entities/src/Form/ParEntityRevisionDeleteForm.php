<?php

namespace Drupal\par_data_entities\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\trance\Form\TranceRevisionDeleteForm;

/**
 * Provides a form for reverting a par_entity revision.
 */
class ParEntityRevisionDeleteForm extends TranceRevisionDeleteForm {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('par_entity'),
      $entity_manager->getStorage('par_entity_type'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $par_entity_revision = NULL) {
    return parent::buildForm($form, $form_state, $par_entity_revision);
  }

}
