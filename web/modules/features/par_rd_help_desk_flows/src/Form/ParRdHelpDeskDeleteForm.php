<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Symfony\Component\Routing\Route;

/**
 * Deleting a partnership.
 */
class ParRdHelpDeskDeleteForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'delete_partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return "Help Desk | Partnership deleted";
  }

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['partnership_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('The partnership application has been deleted'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }
}
