<?php

namespace Drupal\par_data_entities\Form;

use Drupal\Core\Form\FormStateInterface;

use Drupal\trance\Form\TranceRevisionRevertTranslationForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form for reverting a ParEntity revision for a single translation.
 */
class ParEntityRevisionRevertTranslationForm extends TranceRevisionRevertTranslationForm {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('par_entity'),
      $container->get('date.formatter'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $par_entity_revision = NULL, $langcode = NULL) {
    return parent::buildForm($form, $form_state, $par_entity_revision, $langcode);
  }

}
