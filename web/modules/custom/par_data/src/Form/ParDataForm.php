<?php

namespace Drupal\par_data\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Form controller for par_data edit forms.
 *
 * @ingroup par_data
 */
class ParDataForm extends ContentEntityForm {

  /**
   * Get time service.
   *
   * @return \Drupal\Component\Datetime\TimeInterface
   */
  public function getTime() {
    if (!isset($this->time)) {
      $this->time = \Drupal::time();
    }

    return $this->time;
  }

  /**
   * Overrides \Drupal\Core\Entity\EntityForm::prepareEntity().
   *
   * Prepares the entity. Fills in a few default values.
   */
  protected function prepareEntity() {
    $entity = $this->entity;

    if (!$entity->isNew()) {
      $entity->setRevisionLog(NULL);
    }
    // By default always create a new revision.
    $entity->setNewRevision(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    if (empty($form['advanced'])) {
      $form['advanced'] = [
        '#type' => 'vertical_tabs',
        '#weight' => 99,
      ];
    }

    // Add a log field if the "Create new revision" option is checked, or if the
    // current user has the ability to check that option.
    $form['revision_information'] = [
      '#type' => 'details',
      '#title' => $this->t('Revision information'),
      // Open by default when "Create new revision" is checked.
      '#open' => $this->entity->isNewRevision(),
      '#group' => 'advanced',
      '#weight' => 20,
    ];

    $form['revision'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Create new revision'),
      '#default_value' => $this->entity->isNewRevision(),
      '#group' => 'revision_information',
    ];

    // Check the revision log checkbox when the log textarea is filled in.
    // This must not happen if "Create new revision" is enabled by default,
    // since the state would auto-disable the checkbox otherwise.
    if (!$this->entity->isNewRevision()) {
      $form['revision']['#states'] = [
        'checked' => [
          'textarea[name="revision_log"]' => ['empty' => FALSE],
        ],
      ];
    }

    $form['revision_log'] += array(
      '#states' => array(
        'visible' => array(
          ':input[name="revision"]' => array('checked' => TRUE),
        ),
      ),
      '#group' => 'revision_information',
    );

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Published'),
      '#default_value' => $this->entity->status->value,
      '#weight' => 49,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->entity->setRevisionCreationTime($this->getTime()->getRequestTime());
    $this->entity->setRevisionAuthorId(\Drupal::currentUser()->id());
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $entity_type = $entity->getEntityType()->id();

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('revision')) {
      $entity->setNewRevision();
    }
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label content entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label content entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.' . $entity_type . '.edit_form', [
      $entity_type => $entity->id(),
    ]);
  }

}
