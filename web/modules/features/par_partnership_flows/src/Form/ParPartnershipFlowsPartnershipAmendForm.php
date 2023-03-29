<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Form to add legal entities to partnerships.
 */
class ParPartnershipFlowsPartnershipAmendForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /*
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $negotiation
   *   The flow negotiator.
   * @param \Drupal\par_flows\ParFlowDataHandlerInterface $data_handler
   *   The flow data handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   *   The par form builder.
   */
  /*public function __construct(ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler, ParDataManagerInterface $par_data_manager, PluginManagerInterface $plugin_manager, UrlGeneratorInterface $url_generator) {
    parent::__construct($negotiator, $data_handler, $par_data_manager, $plugin_manager, $url_generator);
    $flow = $this->getFlowNegotiator()->getFlow();
    $actions = $flow->getActions();
    if (($key = array_search('save', $actions)) !== false) {
      unset($actions[$key]);
      $flow->setActions($actions);
    }
  }*/
  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $this->pageTitle = 'Update Partnership Information | Add legal entities to the partnership';

    return parent::titleCallback();
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Access according to route and user role.
    switch ($route_match->getRouteName()) {
      case 'par_partnership_flows.authority_amend_select':
      case 'par_partnership_flows.authority_amend_declaration':
      case 'par_partnership_flows.authority_amend_terms':
      case 'par_partnership_flows.authority_amend_complete':
        if (!in_array('par_authority', $account->getRoles())) {
          $this->accessResult = AccessResult::forbidden('The user is not allowed to access the authority partnership amendment page.');
        }
        break;
      default:
        $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    if ($this->getFormId() == 'par_partnership_authority_amend_terms') {
      if (empty($form_state->getValue('terms_authority_agreed'))) {
        $id = $this->getElementId(['terms_authority_agreed'], $form);
        $form_state->setErrorByName($this->getElementName('terms_authority_agreed'), $this->wrapErrorMessage('You must agree to the terms and conditions.', $id));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Conditions and terms have been accepted by the authority.
    if ($this->getFormId() == 'par_partnership_authority_amend_terms') {
      /* @var ParDataPartnership $partnership */
      $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $amend_partnership_legal_entities = $partnership->getPartnershipLegalEntities(FALSE, 'awaiting_review');
      foreach ($amend_partnership_legal_entities as $amend_partnership_legal_entity) {
        $amend_partnership_legal_entity->setPartnershipLegalEntityStatus('confirmed_authority');
        $amend_partnership_legal_entity->save();
      }
    }

    parent::submitForm($form, $form_state);
  }
}
