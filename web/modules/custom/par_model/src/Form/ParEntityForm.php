<?php

namespace Drupal\par_model\Form;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\trance\Form\TranceForm;

/**
 * Form controller for the par_entity edit forms.
 */
class ParEntityForm extends TranceForm {

  public static $entityType = 'par_entity';

  public static $bundleEntityType = 'par_entity_type';

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return parent::create($container, self::$entityType, self::$bundleEntityType);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $account = $this->currentUser();

    $form = parent::buildForm($form, $form_state);

    if (isset($form['revision_information'])) {
      $form['revision_information']['#access'] = $entity->isNewRevision() || $account->hasPermission('administer par entity');
    }

    if (isset($form['revision'])) {
      $form['revision']['#access'] = $account->hasPermission('administer par entity');
    }

    return $form;
  }

}
