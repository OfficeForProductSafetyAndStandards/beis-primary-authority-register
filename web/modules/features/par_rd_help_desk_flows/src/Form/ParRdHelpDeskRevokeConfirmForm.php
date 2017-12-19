<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParDisplayTrait;

/**
 * The confirming the user is authorised to revoke partnerships.
 */
class ParRdHelpDeskRevokeConfirmForm extends ParBaseForm {

  use ParDisplayTrait;

  /**
   * {@inheritdoc}
   */
  protected $flow = 'revoke_partnership';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_rd_help_desk_revoke_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Confirmation | Revoke a partnership';
  }

  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL) {
    // 403 if the partnership is active/approved by RD.
    if ($par_data_partnership->getRawStatus() !== 'confirmed_rd') {
      $this->accessResult = AccessResult::forbidden('The partnership is not active.');
    }

    return parent::accessCallback();
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    // Present partnership info.
    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Revoke the partnership between'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['partnership_info']['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Present partnership info.
    $form['revocation_group'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enter the reason you are revoking this partnership'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['revocation_group']['revocation_reason'] = [
      '#type' => 'textarea',
      '#rows' => 5,
      '#default_value' => $this->getDefaultValues('revocation_reason', FALSE),
    ];

    // Get related entities to revoke alongside partnership.
    $related_entities = $this->getParDataManager()->getRelatedEntities($par_data_partnership);

    $form['entities_pending_revocation'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => $this->t('Documents that will be revoked'),
      '#description' => $this->t('Revoking this partnership will also revoke the following documents'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Loop through related entities.
    foreach ($related_entities as $related_entity_type => $entities) {
      foreach ($entities as $related_entity) {

        if ($related_entity_type === 'par_data_inspection_plan' ||
          $related_entity_type === 'par_data_advice') {
          // Revoke related entity.
          $related_entity_confirm[] = [
            '#type' => 'markup',
            '#markup' => "<div><strong>{$related_entity_type}</strong> {$related_entity->label()}</div>"
          ];
        }

      }
    }

    $form['entities_pending_revocation']['list'] = $this->renderTable($related_entity_confirm);

//    // Auth.
//    $form['partnership_revoke'] = [
//      '#type' => 'fieldset',
//      '#title' => $this->t('Please confirm you are authorised to revoke this partnership'),
//      '#attributes' => ['class' => 'form-group'],
//    ];
//
//    $form['partnership_revoke']['confirm_authorisation_select'] = [
//      '#type' => 'checkbox',
//      '#title' => $this->t('Yes, I am authorised to revoke this partnership'),
//      '#required' => TRUE,
//    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getRouteParam('par_data_partnership');

    // We only want to update the status of active partnerships.
    if ($par_data_partnership->getRawStatus() == 'confirmed_rd') {

      $par_data_partnership->revoke();

      $par_data_partnership->set('revocation_reason', $this->getTempDataValue('revocation_reason'));

      if ($par_data_partnership->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('Revocation reason could not be saved for %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }

      // Get related entities to revoke alongside partnership.
      $related_entities = $this->getParDataManager()->getRelatedEntities($par_data_partnership);

      // Loop through related entities.
      foreach ($related_entities as $related_entity_type => $entities) {
        foreach ($entities as $related_entity) {

          if ($related_entity_type === 'par_data_inspection_plan' ||
              $related_entity_type === 'par_data_advice') {
            // Revoke related entity.
            $related_entity->revoke();
          }

        }
      }

    }
  }

}
