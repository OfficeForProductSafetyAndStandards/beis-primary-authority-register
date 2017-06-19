<?php

namespace Drupal\par_data_entities\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\trance\Form\TranceRevisionRevertForm;

/**
 * Provides a form for reverting a par_entity revision.
 */
class ParEntityRevisionRevertForm extends TranceRevisionRevertForm {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('par_entity'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $par_entity_revision = NULL) {
    return parent::buildForm($form, $form_state, $par_entity_revision);
  }

}
