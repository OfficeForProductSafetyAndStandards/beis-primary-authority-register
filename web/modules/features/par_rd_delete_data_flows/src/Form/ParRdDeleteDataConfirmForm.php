<?php

namespace Drupal\par_rd_delete_data_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * The confirming the user is authorised to delete partnerships.
 */
class ParRdDeleteDataConfirmForm extends ParBaseForm {

  use ParDisplayTrait;

  /**
   * {@inheritdoc}
   */
  protected $flow = 'delete_partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $entity_type = $this->getFlowDataHandler()->getParameter('entity_type');
    $entity_id = $this->getFlowDataHandler()->getParameter('entity_id');
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);
    return 'Help Desk | Delete ' . $entity->label();
  }

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, $entity_type = '', $entity_id = '') {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

    // If partnership has been deleted, we should not be able to re-delete it.
    if (!$entity instanceof ParDataEntityInterface && !$entity->isDeleted()) {
       $this->accessResult = AccessResult::forbidden('The partnership must be deleted to access this page.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type = '', $entity_id = '') {
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

    if ($entity && $entity->isActive()) {
      $form['info'] = [
        '#type' => 'markup',
        '#title' => $this->t('Deletion denied'),
        '#markup' => $this->t('This partnership cannot be deleted because it is active. Please use the revoke process instead.'),
      ];

      return parent::buildForm($form, $form_state);
    }

    $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity_type);
    $entity_display = $entity_view_builder->view($entity, 'full');
    $rendered_entity = $this->getRenderer()->render($entity_display);
    $form['detail'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Full details about this data'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['detail']['text'] = [
      '#type' => 'markup',
      '#markup' => $rendered_entity,
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Change the primary action text.
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Delete');

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

    $entity_type = $this->getFlowDataHandler()->getParameter('entity_type');
    $entity_id = $this->getFlowDataHandler()->getParameter('entity_id');
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

    // We only want to update the status of none active partnerships.
    if ($entity->annihilate()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Deletion reason could not be saved for %form_id');
      $replacements = [
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
